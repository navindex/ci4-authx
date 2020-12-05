<?php

namespace Navindex\Auth\Authorisation;

interface AuthorisationInterface
{
	/**
	 * Returns the latest error string.
	 *
	 * @return null|string|array Error message(s) or null
	 */
	public function error();

	//--------------------------------------------------------------------
	// Actions
	//--------------------------------------------------------------------

	/**
	 * Checks to see if a user has a role.
	 *
	 * Roles can be either a string, with the name of the role, an INT
	 * with the ID of the role, or an array of strings/ids that the
	 * user must belong to ONE of. (It's an OR check not an AND check)
	 *
	 * @param int|string       $user  User ID or name
	 * @param int|string|array $roles Role ID, role name or an array of roles
	 *
	 * @return bool True if the user has any of the roles, false otherwise
	 */
	public function hasRole($user, $roles): bool;

	//--------------------------------------------------------------------

	/**
	 * Checks a user's roles to see if they have the specified permission.
	 *
	 * @param int|string $user       User ID or name
	 * @param int|string $permission Permission ID or name
	 * @param null|bool  $direct     True: direct user permissions only
	 *                               False: both user and role permissions
	 *
	 * @return bool True if the user has the permission, false otherwise
	 */
	public function hasPermission($user, $permission, bool $direct = false): bool;

	//--------------------------------------------------------------------

	/**
	 * Adds a role to the user.
	 *
	 * @param int|string $user User ID or name
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addUserRole($user, $role): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a single user role.
	 *
	 * @param int|string $user User ID or name
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeUserRole($user, $role): bool;

	//--------------------------------------------------------------------

	/**
	 * Adds a permission directly to the user.
	 *
	 * @param int|string $user       User ID or name
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addUserPermission($user, $permission): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a user's direct permission.
	 *
	 * @param int|string $user       User ID or name
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeUserPermission($user, $permission): bool;

	//--------------------------------------------------------------------

	/**
	 * Adds a single permission to a single role.
	 *
	 * @param int|string $role       Role ID or name
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addRolePermission($role, $permission): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a single permission from a role.
	 *
	 * @param int|string $role       Role ID or name
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeRolePermission($role, $permission): bool;

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
	public function role($role): ?object;

	//--------------------------------------------------------------------

	/**
	 * Grabs an array of all roles.
	 *
	 * @return array Array of role objects
	 */
	public function roles(): array;

	//--------------------------------------------------------------------

	/**
	 * Creates a new role.
	 *
	 * @param string $name        Role name
	 * @param string $description Role description
	 *
	 * @return false|int Role ID or false
	 */
	public function createRole(string $name, string $description);

	//--------------------------------------------------------------------

	/**
	 * Updates a single role's information.
	 *
	 * @param int    $id          Role ID
	 * @param string $name        Role name
	 * @param string $description Role description
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function updateRole(int $id, string $name, string $description): bool;

	//--------------------------------------------------------------------

	/**
	 * Deletes a single role and removes that role from all users.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function deleteRole($role): bool;

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
	public function permission($permission): ?object;

	//--------------------------------------------------------------------

	/**
	 * Returns an array of all permissions in the system.
	 *
	 * @return array Array of permission objects
	 */
	public function permissions(): array;

	//--------------------------------------------------------------------

	/**
	 * Creates a single permission.
	 *
	 * @param string $name        Permission name
	 * @param string $description Permission description
	 *
	 * @return false|int Permission ID or false
	 */
	public function createPermission(string $name, string $description);

	//--------------------------------------------------------------------

	/**
	 * Updates the details for a single permission.
	 *
	 * @param int    $id          Permission ID
	 * @param string $name        Permission name
	 * @param string $description Permission description
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function updatePermission(int $id, string $name, string $description): bool;

	//--------------------------------------------------------------------

	/**
	 * Deletes a single permission and removes that permission from all groups.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function deletePermission($permission): bool;
}
