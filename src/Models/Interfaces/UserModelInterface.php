<?php

namespace Navindex\Auth\Models\Interfaces;

use Navindex\Auth\Models\Interfaces\AuthModelInterface;

interface UserModelInterface extends AuthModelInterface
{
	//--------------------------------------------------------------------
	// User
	//--------------------------------------------------------------------

	/**
	 * Retrieves a user by its credentials.
	 *
	 * @param array $credentials Associative array of credentials
	 *
	 * @return null|mixed User record or null
	 */
	public function fetchByCredentials(array $credentials);

	//--------------------------------------------------------------------

	/**
	 * A convenience method that will attempt to determine whether the
	 * data should be inserted or updated. Will work with either
	 * an array or object. When using with custom class objects,
	 * you must ensure that the class will provide access to the class
	 * variables, even if through a magic method.
	 *
	 * @param array|object $data Record entity
	 *
	 * @throws \ReflectionException
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function save($data): bool;

	//--------------------------------------------------------------------

	/**
	 * Convenience function to filter inactive users.
	 *
	 * @return mixed Self
	 */
	public function inactive();

	//--------------------------------------------------------------------
	// Role
	//--------------------------------------------------------------------

	/**
	 * Retrieves all roles of a single user.
	 *
	 * @param int|string $user User ID or username
	 *
	 * @return array Array of role objects
	 */
	public function getRoles($user): array;

	//--------------------------------------------------------------------

	/**
	 * Checks whether a specific user has a specific role.
	 *
	 * @param int|string $user User ID or username
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the user has the role, false otherwise
	 */
	public function hasRole($user, $role): bool;

	//--------------------------------------------------------------------

	/**
	 * Adds a user role.
	 *
	 * @param int|string $user User ID or username
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addRole($user, $role): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a user role.
	 *
	 * @param int|string $user User ID or username
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeRole($user, $role): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a single user from all roles.
	 *
	 * @param int|string $user User ID or username
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeAllRoles($user): bool;

	//--------------------------------------------------------------------
	// Permission
	//--------------------------------------------------------------------

	/**
	 * Retrieves all permissions of a single user.
	 *
	 * @param int|string $user   User ID or username
	 * @param null|bool  $direct True: direct user permissions only
	 *                           False: both user and role permissions
	 *
	 * @return array Array of permission objects
	 */
	public function getPermissions($user, bool $direct = false): array;

	//--------------------------------------------------------------------

	/**
	 * Checks whether a specific permission has a specific permission.
	 *
	 * @param int|string $user       User ID or username
	 * @param int|string $permission Permission ID or name
	 * @param null|bool  $direct     True: direct user permissions only
	 *                               False: both user and role permissions
	 *
	 * @return bool True if the user has the permission, false otherwise
	 */
	public function hasPermission($user, $permission, bool $direct = false): bool;

	//--------------------------------------------------------------------

	/**
	 * Adds a permission directly to a user.
	 *
	 * @param int|string $user       User ID or username
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addPermission($user, $permission): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a direct permission from the user.
	 *
	 * @param int|string $user       User ID or username
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removePermission($user, $permission): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes a single user from all permissions.
	 *
	 * @param int|string $user User ID or username
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeAllPermissions($user): bool;

	//--------------------------------------------------------------------
	// Attempt
	//--------------------------------------------------------------------

	/**
	 * Logs a login attempt for posterity sake.
	 *
	 * @param string      $email     Email address
	 * @param bool        $success   Was the login successful?
	 * @param null|int    $userId    User ID
	 * @param null|string $ipAddress IP address
	 * @param null|string $userAgent User agent
	 */
	public function logLoginAttempt(
		string $email,
		bool $success,
		int $userID = null,
		string $ipAddress = null,
		string $userAgent = null
	): void;

	//--------------------------------------------------------------------

	/**
	 * Logs a password reset attempt for posterity sake.
	 *
	 * @param string      $email     Email address
	 * @param null|string $token     Password reset token
	 * @param null|string $ipAddress IP address
	 * @param null|string $userAgent User agent
	 */
	public function logResetAttempt(
		string $email,
		string $token = null,
		string $ipAddress = null,
		string $userAgent = null
	): void;

	//--------------------------------------------------------------------

	/**
	 * Logs an activation attempt for posterity sake.
	 *
	 * @param null|string $token     Password reset token
	 * @param null|string $ipAddress IP address
	 * @param null|string $userAgent User agent
	 */
	public function logActivationAttempt(
		string $token = null,
		string $ipAddress = null,
		string $userAgent = null
	): void;

	//--------------------------------------------------------------------
	// Default role
	//--------------------------------------------------------------------

	/**
	 * Sets the role to assign any users created.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return mixed Self
	 */
	public function setDefaultRole($role);

	//--------------------------------------------------------------------

	/**
	 * Clears the role to assign to newly created users.
	 *
	 * @return mixed Self
	 */
	public function unsetDefaultRole();
}
