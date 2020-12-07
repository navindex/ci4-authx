<?php

namespace Navindex\AuthX\Models\Types;

use Navindex\AuthX\Entities\Type;
use Navindex\AuthX\Models\Base\TypeModel;
use Navindex\AuthX\Models\Interfaces\PermissionModelInterface;
use Navindex\AuthX\Models\Junctions\RolePermissionModel;
use Navindex\AuthX\Models\Junctions\UserPermissionModel;
use Navindex\AuthX\Models\Lists\RolePermissionListModel;
use Navindex\AuthX\Models\Lists\UserPermissionListModel;

class PermissionModel extends TypeModel implements PermissionModelInterface
{
	protected $table = 'permission';

	//--------------------------------------------------------------------
	// Permission
	//--------------------------------------------------------------------

	/**
	 * Retrieves all available permissions.
	 *
	 * @return array Array of permission objects
	 */
	public function fetchAll(): array
	{
		return $this->findAll();
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves a single permission.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return null|\App\Entities\Type Permission object or null
	 */
	public function fetch($permission): ?Type
	{
		return $this->getType($permission);
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves the permission ID.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return null|int Permission ID or null
	 */
	public function getId($permission): ?int
	{
		return $this->getTypeId($permission);
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves the permission name.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return null|string Permission name or null
	 */
	public function getName($permission): ?string
	{
		return $this->getTypeName($permission);
	}

	//--------------------------------------------------------------------

	/**
	 * Creates a new permission.
	 *
	 * @param string $name        Permission name
	 * @param string $description Permission description
	 *
	 * @return false|int Permission ID or false
	 */
	public function add(string $name, string $description)
	{
		return false !== $this->addType($name, $description);
	}

	//--------------------------------------------------------------------

	/**
	 * Updates a permission.
	 *
	 * @param int    $id          Permission ID
	 * @param string $name        Permission name
	 * @param string $description Permission description
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function change(int $id, string $name, string $description): bool
	{
		return $this->updateType($id, $name, $description);
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a permission.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function remove($permission): bool
	{
		return false !== $this->deleteType($permission);
	}

	//--------------------------------------------------------------------
	// User
	//--------------------------------------------------------------------

	/**
	 * Retrieves all users having a single direct permission.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return array Array of user objects
	 */
	public function getUsers($permission): array
	{
		$permissionId = $this->getTypeId($permission);

		if (!\is_numeric($permissionId)) {
			return [];
		}

		return model(UserPermissionListModel::class)->getPermissionUsers($permissionId);
	}

	//--------------------------------------------------------------------

	/**
	 * Checks whether a specific user has a specific direct permission.
	 *
	 * @param int|string $permission Permission ID or name
	 * @param int        $userId     User ID
	 *
	 * @return bool True if the user directly has the permission, false otherwise
	 */
	public function hasUser($permission, int $userId): bool
	{
		$permissionId = $this->getTypeId($permission);

		if (!\is_numeric($permissionId)) {
			return false;
		}

		return !empty(model(UserPermissionModel::class)->find([$userId, $permissionId]));
	}

	//--------------------------------------------------------------------

	/**
	 * Adds a direct permission to a user.
	 *
	 * @param int|string $permission Permission ID or name
	 * @param int        $userId     User ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addUser($permission, int $userId): bool
	{
		$permissionId = $this->getTypeId($permission);

		if (!\is_numeric($permissionId)) {
			return false;
		}

		return model(UserPermissionModel::class)->connect($userId, $permissionId);
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a user's direct permission.
	 *
	 * @param int|string $permission Permission ID or name
	 * @param int        $userId     User ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeUser($permission, int $userId): bool
	{
		$permissionId = $this->getTypeId($permission);

		if (!\is_numeric($permissionId)) {
			return false;
		}

		return model(UserPermissionModel::class)->disconnect($userId, $permissionId);
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a single direct permission from all users.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeAllUsers($permission): bool
	{
		$permissionId = $this->getTypeId($permission);

		if (!\is_numeric($permissionId)) {
			return false;
		}

		return false !== model(UserPermissionModel::class)->deleteRight($permissionId);
	}

	//--------------------------------------------------------------------
	// Role
	//--------------------------------------------------------------------

	/**
	 * Retrieves all roles of a single permission.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return array Array of role objects
	 */
	public function getRoles($permission): array
	{
		$permissionId = $this->getTypeId($permission);

		if (!\is_numeric($permissionId)) {
			return [];
		}

		return model(RolePermissionListModel::class)->getPermissionRoles($permissionId);
	}

	//--------------------------------------------------------------------

	/**
	 * Checks whether a specific permission belongs to a specific role.
	 *
	 * @param int|string $permission Permission ID or name
	 * @param int        $roleId     Role ID
	 *
	 * @return bool True if the role has the permission, false otherwise
	 */
	public function hasRole($permission, int $roleId): bool
	{
		$permissionId = $this->getTypeId($permission);

		if (!\is_numeric($permissionId)) {
			return false;
		}

		return !empty(model(RolePermissionModel::class)->find([$roleId, $permissionId]));
	}

	//--------------------------------------------------------------------

	/**
	 * Adds a role to a permission.
	 *
	 * @param int|string $permission Permission ID or name
	 * @param int        $roleId     Role ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addRole($permission, int $roleId): bool
	{
		$permissionId = $this->getTypeId($permission);

		if (!\is_numeric($permissionId)) {
			return false;
		}

		return model(RolePermissionModel::class)->connect($roleId, $permissionId);
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a role of a permission.
	 *
	 * @param int|string $permission Permission ID or name
	 * @param int        $roleId     Role ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeRole($permission, int $roleId): bool
	{
		$permissionId = $this->getTypeId($permission);

		if (!\is_numeric($permissionId)) {
			return false;
		}

		return model(RolePermissionModel::class)->disconnect($roleId, $permissionId);
	}

	//--------------------------------------------------------------------

	/**
	 * Removes all roles of a permission.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeAllRoles($permission): bool
	{
		$permissionId = $this->getTypeId($permission);

		if (!\is_numeric($permissionId)) {
			return false;
		}

		return model(RolePermissionModel::class)->deleteRight($permissionId);
	}
}
