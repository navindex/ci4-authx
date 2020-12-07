<?php

namespace Navindex\AuthX\Models\Interfaces;

use Navindex\AuthX\Models\Interfaces\AuthModelInterface;

interface RoleModelInterface extends AuthModelInterface
{
	//--------------------------------------------------------------------
	// Role
	//--------------------------------------------------------------------

	/**
	 * Creates a new role.
	 *
	 * @param string $name        Role name
	 * @param string $description Role description
	 *
	 * @return false|int Role ID or false
	 */
	public function add(string $name, string $description);

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
	public function change(int $id, string $name, string $description): bool;

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
	public function getUsers($role): array;

	//--------------------------------------------------------------------

	/**
	 * Checks whether a specific role belongs to a specific user.
	 *
	 * @param int|string $role   Role ID or name
	 * @param int        $userId User ID
	 *
	 * @return bool True if the user has the role, false otherwise
	 */
	public function hasUser($role, int $userId): bool;

	//--------------------------------------------------------------------

	/**
	 * Adds a role to a user.
	 *
	 * @param int|string $role   Role ID or name
	 * @param int        $userId User ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addUser($role, int $userId): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a user role.
	 *
	 * @param int|string $role   Role ID or name
	 * @param int        $userId User ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeUser($role, int $userId): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a single role from all users.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeAllUsers($role): bool;

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
	public function getPermissions($role): array;

	//--------------------------------------------------------------------

	/**
	 * Checks whether a specific role has a specific permission.
	 *
	 * @param int|string $role         Role ID or name
	 * @param int        $permissionId Permission ID
	 *
	 * @return bool True if the role has the permission, false otherwise
	 */
	public function hasPermission($role, int $permissionId): bool;

	//--------------------------------------------------------------------

	/**
	 * Adds a permission to a role.
	 *
	 * @param int|string $role         Role ID or name
	 * @param int        $permissionId Permission ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addPermission($role, int $permissionId): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a permission of a role.
	 *
	 * @param int|string $role         Role ID or name
	 * @param int        $permissionId Permission ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removePermission($role, int $permissionId): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes all permissions of a role.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeAllPermissions($role): bool;
}
