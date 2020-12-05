<?php

if (!function_exists('logged_in')) {
	/**
	 * Checks to see if the user is logged in.
	 *
	 * @return bool True if the user logged in, false otherwise
	 */
	function logged_in(): bool
	{
		return service('authentication')->check();
	}
}

if (!function_exists('user')) {
	/**
	 * Returns the User instance for the current logged in user.
	 *
	 * @return null|\Navindex\Auth\Entities\UserInterface User entity
	 */
	function user()
	{
		$authenticate = service('authentication');
		$authenticate->check();
		return $authenticate->user();
	}
}

if (!function_exists('user_id')) {
	/**
	 * Returns the User ID for the current logged in user.
	 *
	 * @return null|int User ID
	 */
	function user_id()
	{
		$authenticate = service('authentication');
		$authenticate->check();
		return $authenticate->id();
	}
}

if (!function_exists('has_role')) {
	/**
	 * Ensures that the current user has at least one of the passed in
	 * roles. The roles can be passed in as either ID's or role names.
	 * You can pass either a single item or an array of items.
	 *
	 * Example:
	 *  has_role([1, 2, 3]);
	 *  has_role(14);
	 *  has_role('admin');
	 *  has_role( ['admin', 'moderator'] );
	 *
	 * @param int|string|array $roles
	 *
	 * @return bool True if the user has any of the roles, false otherwise
	 */
	function has_role($roles): bool
	{
		$authenticate = service('authentication');
		$authorize = service('authorization');

		if ($authenticate->check()) {
			return $authorize->hasRole($authenticate->id(), $roles);
		}

		return false;
	}
}

if (!function_exists('has_permission')) {
	/**
	 * Ensures that the current user has the passed in permission.
	 * The permission can be passed in either as an ID or name.
	 *
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool
	 */
	function has_permission($permission): bool
	{
		$authenticate = service('authentication');
		$authorize = service('authorization');

		if ($authenticate->check()) {
			return $authorize->hasPermission($authenticate->id(), $permission) ?? false;
		}

		return false;
	}
}
