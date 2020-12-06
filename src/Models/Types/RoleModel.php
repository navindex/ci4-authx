<?php

namespace Navindex\Auth\Models\Types;

use Navindex\Auth\Entities\Type;
use Navindex\Auth\Models\Base\TypeModel;
use Navindex\Auth\Models\Interfaces\RoleModelInterface;
use Navindex\Auth\Models\Junctions\RolePermissionModel;
use Navindex\Auth\Models\Junctions\UserRoleModel;
use Navindex\Auth\Models\Lists\RolePermissionListModel;
use Navindex\Auth\Models\Lists\UserRoleListModel;

class RoleModel extends TypeModel implements RoleModelInterface
{
	protected $table = 'role';

	//--------------------------------------------------------------------
	// Role
	//--------------------------------------------------------------------

	/**
	 * Retrieves all available roles.
	 *
	 * @return array Array of role objects
	 */
	public function fetchAll(): array
	{
		return $this->findAll();
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves a single role.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return null|\App\Entities\Type Role object or null
	 */
	public function fetch($role): ?Type
	{
		return $this->getType($role);
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves the role ID.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return null|int Role ID or null
	 */
	public function getId($role): ?int
	{
		return $this->getTypeId($role);
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves the role name.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return null|string Role name or null
	 */
	public function getName($role): ?string
	{
		return $this->getTypeName($role);
	}

	//--------------------------------------------------------------------

	/**
	 * Creates a new role.
	 *
	 * @param string $name        Role name
	 * @param string $description Role description
	 *
	 * @return false|int Role ID or false
	 */
	public function add(string $name, string $description)
	{
		return $this->addType($name, $description);
	}

	//--------------------------------------------------------------------

	/**
	 * Updates a role.
	 *
	 * @param int    $id          Role ID
	 * @param string $name        Role name
	 * @param string $description Role description
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function change(int $id, string $name, string $description): bool
	{
		return $this->updateType($id, $name, $description);
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a role.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function remove($role): bool
	{
		return $this->deleteType($role);
	}

	//--------------------------------------------------------------------
	// User
	//--------------------------------------------------------------------

	/**
	 * Retrieves all users having a single role.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return array Array of user objects
	 */
	public function getUsers($role): array
	{
		$roleId = $this->getTypeId($role);

		if (!\is_numeric($roleId)) {
			return [];
		}

		return model(UserRoleListModel::class)->getRoleUsers($roleId);
	}

	//--------------------------------------------------------------------

	/**
	 * Checks whether a specific role belongs to a specific user.
	 *
	 * @param int|string $role   Role ID or name
	 * @param int        $userId User ID
	 *
	 * @return bool True if the user has the role, false otherwise
	 */
	public function hasUser($role, int $userId): bool
	{
		$roleId = $this->getTypeId($role);

		if (!\is_numeric($roleId)) {
			return false;
		}

		return !empty(model(UserRoleModel::class)->find([$userId, $roleId]));
	}

	//--------------------------------------------------------------------

	/**
	 * Adds a role to a user.
	 *
	 * @param int|string $role   Role ID or name
	 * @param int        $userId User ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addUser($role, int $userId): bool
	{
		$roleId = $this->getTypeId($role);

		if (!\is_numeric($roleId)) {
			return false;
		}

		return model(UserRoleModel::class)->connect($userId, $roleId);
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a user role.
	 *
	 * @param int|string $role   Role ID or name
	 * @param int        $userId User ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeUser($role, int $userId): bool
	{
		$roleId = $this->getTypeId($role);

		if (!\is_numeric($roleId)) {
			return false;
		}

		return model(UserRoleModel::class)->disconnect($userId, $roleId);
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a single role from all users.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeAllUsers($role): bool
	{
		$roleId = $this->getTypeId($role);

		if (!\is_numeric($roleId)) {
			return false;
		}

		return model(UserRoleModel::class)->deleteRight($roleId);
	}

	//--------------------------------------------------------------------
	// Permission
	//--------------------------------------------------------------------

	/**
	 * Retrieves all permissions of a single role.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return array Array of permission objects
	 */
	public function getPermissions($role): array
	{
		$roleId = $this->getTypeId($role);

		if (!\is_numeric($roleId)) {
			return [];
		}

		return model(RolePermissionListModel::class)->getRolePermissions($roleId);
	}

	//--------------------------------------------------------------------

	/**
	 * Checks whether a specific role has a specific permission.
	 *
	 * @param int|string $role         Role ID or name
	 * @param int        $permissionId Permission ID
	 *
	 * @return bool True if the role has the permission, false otherwise
	 */
	public function hasPermission($role, int $permissionId): bool
	{
		$roleId = $this->getTypeId($role);

		if (!\is_numeric($roleId)) {
			return false;
		}

		return !empty(model(RolePermissionModel::class)->find([$roleId, $permissionId]));
	}

	//--------------------------------------------------------------------

	/**
	 * Adds a permission to a role.
	 *
	 * @param int|string $role         Role ID or name
	 * @param int        $permissionId Permission ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addPermission($role, int $permissionId): bool
	{
		$roleId = $this->getTypeId($role);

		if (!\is_numeric($roleId)) {
			return false;
		}

		return model(RolePermissionModel::class)->connect($roleId, $permissionId);
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a permission of a role.
	 *
	 * @param int|string $role         Role ID or name
	 * @param int        $permissionId Permission ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removePermission($role, int $permissionId): bool
	{
		$roleId = $this->getTypeId($role);

		if (!\is_numeric($roleId)) {
			return false;
		}

		return model(RolePermissionModel::class)->disconnect($roleId, $permissionId);
	}

	//--------------------------------------------------------------------

	/**
	 * Removes all permissions of a role.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeAllPermissions($role): bool
	{
		$roleId = $this->getTypeId($role);

		if (!\is_numeric($roleId)) {
			return false;
		}

		return model(RolePermissionModel::class)->deleteLeft($roleId);
	}
}
