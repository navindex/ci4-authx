<?php

namespace Navindex\AuthX\Authorisation;

use CodeIgniter\Events\Events;
use Navindex\AuthX\Authorisation\AuthorisationInterface;
use Navindex\AuthX\Authorisation\BaseAuthorisation;
use Navindex\AuthX\Models\Interfaces\PermissionModelInterface;
use Navindex\AuthX\Models\Interfaces\RoleModelInterface;
use Navindex\AuthX\Models\Interfaces\UserModelInterface;

class FlatAuthorisation extends BaseAuthorisation implements AuthorisationInterface
{
	/**
	 * Role model.
	 *
	 * @var \Navindex\AuthX\Models\RoleModelInterface
	 */
	protected $roleModel;

	/**
	 * Permission model.
	 *
	 * @var \Navindex\AuthX\Models\PermissionModelInterface
	 */
	protected $permissionModel;

	/**
	 * User model.
	 *
	 * @var \Navindex\AuthX\Models\UserModelInterface
	 */
	protected $userModel;

	//--------------------------------------------------------------------

	public function __construct(RoleModelInterface $roleModel, PermissionModelInterface $permModel)
	{
		$this->roleModel = $roleModel;
		$this->permissionModel = $permModel;
	}

	//--------------------------------------------------------------------

	/**
	 * Allows the consuming application to pass in a reference to the
	 * model that should be used.
	 *
	 * @param $model
	 *
	 * @return \Navindex\AuthX\Authorisation\BaseAuthorisation
	 */
	public function setUserModel(UserModelInterface $model): self
	{
		$this->userModel = $model;

		return $this;
	}

	//--------------------------------------------------------------------
	// Actions
	//--------------------------------------------------------------------

	/**
	 * Checks to see if a user has a role.
	 *
	 * Roles can be either a string, with the name of the role, an INT
	 * with the ID of the role, or an array of strings/IDs that the
	 * user must belong to ONE of. (It's an OR check not an AND check.)
	 *
	 * @param int|string       $user  User ID or name
	 * @param array|int|string $roles Role ID, role name or an array of roles
	 *
	 * @return bool True if the user has any of the roles, false otherwise
	 */
	public function hasRole($user, $roles): bool
	{
		if (!\is_array($roles)) {
			$roles = [$roles];
		}

		if (empty($user) || (!\is_string($user) && !\is_numeric($user))) {
			return false;
		}

		$userRoles = $this->userModel->getRoles($user);

		if (empty($userRoles)) {
			return false;
		}

		foreach ($roles as $role) {
			if (\is_numeric($role)) {
				$ids = \array_column($userRoles, 'id');
				if (\in_array($role, $ids)) {
					return true;
				}
			} elseif (\is_string($role)) {
				$names = \array_column($userRoles, 'name');

				if (\in_array($role, $names)) {
					return true;
				}
			}
		}

		return false;
	}

	//--------------------------------------------------------------------

	/**
	 * Checks a user and its roles to see if they have the specified permission.
	 *
	 * @param int|string $user       User ID or name
	 * @param int|string $permission Permission ID or name
	 * @param null|bool  $direct     True: direct user permissions only
	 *                               False: both user and role permissions
	 *
	 * @return bool True is the user has the permission, false otherwise
	 */
	public function hasPermission($user, $permission, bool $direct = false): bool
	{
		if (empty($permission) || (!\is_string($permission) && !\is_numeric($permission))) {
			return false;
		}

		if (empty($user) || (!\is_string($user) && !\is_numeric($user))) {
			return false;
		}

		// Get the Permission ID
		$permissionId = $this->permissionModel->getID($permission);

		if (!\is_numeric($permissionId)) {
			return false;
		}

		return $this->userModel->hasPermission($user, $permissionId, $direct);
	}

	//--------------------------------------------------------------------

	/**
	 * Adds a role to the user.
	 *
	 * @param int|string $user User ID or name
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addUserRole($user, $role): bool
	{
		if (empty($user) || !\is_numeric($user)) {
			return false;
		}

		if (empty($role) || (!\is_numeric($role) && !\is_string($role))) {
			return false;
		}

		$roleId = $this->roleModel->getID($role);

		if (!Events::trigger('beforeAddingUserRole', $userid, $roleId)) {
			return false;
		}

		if (!\is_numeric($roleId)) {
			return false;
		}

		if (!$this->userModel->addRole($userid, $roleId)) {
			$this->error = $this->userModel->errors();

			return false;
		}

		Events::trigger('afterUserRoleAdded', $userid, $roleId);

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a user's role.
	 *
	 * @param int|string $user User ID or name
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True is the user has the permission, false otherwise
	 */
	public function removeUserRole($user, $role): bool
	{
		if (empty($user) || !\is_numeric($user)) {
			return false;
		}

		if (empty($role) || (!\is_numeric($role) && !\is_string($role))) {
			return false;
		}

		$roleId = $this->roleModel->getID($role);

		if (!\is_numeric($roleId)) {
			return false;
		}

		if (!Events::trigger('beforeRemovingUserRole', $userId, $roleId)) {
			return false;
		}

		if (!$this->userModel->removeRole($userId, $roleId)) {
			$this->error = $this->userModel->errors();

			return false;
		}

		Events::trigger('afterUserRoleRemoved', $userId, $roleId);

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Adds a single permission to a single group.
	 *
	 * @param int|string $role       Role ID or name
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True is the user has the permission, false otherwise
	 */
	public function addRolePermission($role, $permission): bool
	{
		$roleId = $this->roleModel->getID($role);
		$permissionId = $this->permissionModel->getID($permission);

		if (!\is_numeric($roleId) || !\is_numeric($permissionId)) {
			return false;
		}

		if (!$this->roleModel->addPermission($roleId, $permissionId)) {
			$this->error = $this->roleModel->errors();

			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a single permission from a group.
	 *
	 * @param int|string $role       Role ID or name
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True is the user has the permission, false otherwise
	 */
	public function removeRolePermission($role, $permission): bool
	{
		$roleId = $this->roleModel->getID($role);
		$permissionId = $this->permissionModel->getID($permission);

		if (!\is_numeric($roleId) || !\is_numeric($permissionId)) {
			return false;
		}

		// Remove it!
		if (!$this->roleModel->removePermission($roleId, $permissionId)) {
			$this->error = $this->roleModel->errors();

			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Assigns a single permission to a user, irregardless of permissions
	 * assigned by roles. This is saved to the user's meta information.
	 *
	 * @param int|string $permission
	 * @param int        $userId
	 *
	 * @return bool|int
	 */
	public function addPermissionToUser($permission, int $userId)
	{
		$permissionId = $this->permissionModel->getID($permission);

		if (!\is_numeric($permissionId)) {
			return null;
		}

		if (!Events::trigger('beforeAddPermissionToUser', $userId, $permissionId)) {
			return false;
		}

		$user = $this->userModel->find($userId);

		if (!$user) {
			$this->error = lang('Auth.userNotFound', [$userId]);

			return false;
		}

		$permissions = $user->getPermissions();

		if (!\in_array($permissionId, $permissions)) {
			$this->permissionModel->addPermissionToUser($permissionId, $user->id);
		}

		Events::trigger('didAddPermissionToUser', $userId, $permissionId);

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a single permission from a user. Only applies to permissions
	 * that have been assigned with addPermissionToUser, not to permissions
	 * inherited based on groups they belong to.
	 *
	 * @param int/string $permission
	 * @param int        $userId
	 *
	 * @return null|bool|mixed
	 */
	public function removePermissionFromUser($permission, int $userId)
	{
		$permissionId = $this->permissionModel->getID($permission);

		if (!\is_numeric($permissionId)) {
			return false;
		}

		if (empty($userId) || !\is_numeric($userId)) {
			return null;
		}

		$userId = (int) $userId;

		if (!Events::trigger('beforeRemovePermissionFromUser', $userId, $permissionId)) {
			return false;
		}

		return $this->permissionModel->removePermissionFromUser($permissionId, $userId);
	}

	//--------------------------------------------------------------------
	// Roles
	//--------------------------------------------------------------------

	/**
	 * Grabs the details about a single role.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return null|object Role object or null
	 */
	public function role($role): ?object
	{
		if (\is_numeric($role)) {
			return $this->roleModel->find((int) $role);
		}

		return $this->roleModel->where('name', $role)->first();
	}

	//--------------------------------------------------------------------

	/**
	 * Grabs an array of all roles.
	 *
	 * @return array Array of role objects
	 */
	public function roles(): array
	{
		return $this->roleModel->getRoles();
	}

	//--------------------------------------------------------------------

	/**
	 * Creates a new role.
	 *
	 * @param string $name        Role name
	 * @param string $description Role description
	 *
	 * @return bool|int Role ID or false
	 */
	public function createRole(string $name, string $description)
	{
		$id = $this->roleModel->addRole($name, $description);

		if (!\is_numeric($id)) {
			$this->error = $this->roleModel->errors();

			return false;
		}

		return (int) $id;
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a single role and removes that role from all users.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function deleteRole($role): bool
	{
		// Remove all permissions of the role
		$this->roleModel->removeRolePermissions($roleId);

		// Remove role from all users
		$this->roleModel->removeRoleUsers($roleId);

		if (!$this->roleModel->deleteRole($roleId)) {
			$this->error = $this->roleModel->errors();

			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Updates a single role's information.
	 *
	 * @param        $id
	 * @param        $name
	 * @param string $description
	 *
	 * @return mixed
	 */
	public function updateRole(int $id, string $name, string $description)
	{
		$description = $description ?? \ucfirst($name);

		if (!$this->roleModel->updateRole($id, $name, $description)) {
			$this->error = $this->roleModel->errors();

			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------
	// Permissions
	//--------------------------------------------------------------------

	/**
	 * Returns the details about a single permission.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return null|object Permission object or null
	 */
	public function permission($permission): ?object
	{
		return $this->permissionModel->getPermission($permission);
	}

	//--------------------------------------------------------------------

	/**
	 * Returns an array of all permissions in the system.
	 *
	 * @return array
	 */
	public function permissions(): array
	{
		return $this->permissionModel->getPermissions();
	}

	//--------------------------------------------------------------------

	/**
	 * Creates a single permission.
	 *
	 * @param string $name        Permission name
	 * @param string $description Permission description
	 *
	 * @return bool|int Permission ID or false
	 */
	public function createPermission(string $name, string $description)
	{
		$id = $this->permissionModel->addPermission($name, $description);

		if (!\is_numeric($id)) {
			$this->error = $this->permissionModel->errors();

			return false;
		}

		return (int) $id;
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a single permission and removes that permission from all groups.
	 *
	 * @param int $permissionId
	 *
	 * @return mixed
	 */
	public function deletePermission(int $permissionId)
	{
		// Remove permission from all roles
		$this->permissionModel->removePermissionRoles($permissionId);

		// Remove permission from all users
		$this->permissionModel->removePermissionUsers($permissionId);

		if (!$this->permissionModel->deletePermission($permissionId)) {
			$this->error = $this->permissionModel->errors();

			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Updates the details for a single permission.
	 *
	 * @param int    $id          Permission ID
	 * @param string $name        Permission name
	 * @param string $description Permission description
	 *
	 * @return bool
	 */
	public function updatePermission(int $id, string $name, string $description): bool
	{
		$description = $description ?? \ucfirst($name);

		if (!$this->permissionModel->updatePermission($id, $name, $description)) {
			$this->error = $this->permissionModel->errors();

			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns an array of all permissions in the system for a role.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return array Array of role permissions
	 */
	public function rolePermissions($role): array
	{
		$roleId = $this->roleModel->getId($role);

		if (empty($roleId)) {
			return [];
		}

		return $this->roleModel->getRolePermissions($roleId);
	}
}
