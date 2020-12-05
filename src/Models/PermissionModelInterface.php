<?php

namespace Navindex\Auth\Models;

use Navindex\Auth\Models\AuthModelInterface;

interface PermissionModelInterface extends AuthModelInterface
{
	//--------------------------------------------------------------------
	// Permission
	//--------------------------------------------------------------------

	/**
	 * Creates a new permission.
	 *
	 * @param string $name        Permission name
	 * @param string $description Permission description
	 *
	 * @return false|int Permission ID or false
	 */
	public function add(string $name, string $description);

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
	public function change(int $id, string $name, string $description): bool;

	//--------------------------------------------------------------------
	// User
	//--------------------------------------------------------------------

	/**
	 * Retrieves all users having a single permission.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return array Array of user objects
	 */
	public function getUsers($permission): array;

	//--------------------------------------------------------------------

	/**
	 * Checks whether a specific user has a specific permission.
	 *
	 * @param int|string $permission Permission ID or name
	 * @param int        $userId     User ID
	 *
	 * @return bool True if the user has the permission directly, false otherwise
	 */
	public function hasUser($permission, int $userId): bool;

	//--------------------------------------------------------------------

	/**
	 * Adds a permission to a user.
	 *
	 * @param int|string $permission   Permission ID or name
	 * @param int        $userId User ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addUser($permission, int $userId): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a user permission.
	 *
	 * @param int|string $permission   Permission ID or name
	 * @param int        $userId User ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeUser($permission, int $userId): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a single permission from all users.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeAllUsers($permission): bool;

	//--------------------------------------------------------------------
	// Role
	//--------------------------------------------------------------------

	/**
	 * Retrieves all roles having a single permission.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return array Array of role objects
	 */
	public function getRoles($permission): array;

	//--------------------------------------------------------------------

	/**
	 * Checks whether a specific permission belongs to a specific role.
	 *
	 * @param int|string $permission Permission ID or name
	 * @param int        $roleId     Role ID
	 *
	 * @return bool True if the role has the permission, false otherwise
	 */
	public function hasRole($permission, int $roleId): bool;

	//--------------------------------------------------------------------

	/**
	 * Adds a permission to a role.
	 *
	 * @param int|string $permission   Permission ID or name
	 * @param int        $roleId Role ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addRole($permission, int $roleId): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a role permission.
	 *
	 * @param int|string $permission   Permission ID or name
	 * @param int        $roleId Role ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeRole($permission, int $roleId): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a single permission from all roles.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeAllRoles($permission): bool;
}
