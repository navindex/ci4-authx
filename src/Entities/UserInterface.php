<?php

namespace Navindex\AuthX\Entities;

/**
 * User entity interface.
 */
interface UserInterface
{
	/**
	 * Returns the user ID.
	 *
	 * @return null|int User ID or null
	 */
	public function getUserId(): ?int;

	//--------------------------------------------------------------------

	/**
	 * Returns the user's password hash.
	 *
	 * @return null|string Password hash or null
	 */
	public function getPassword(): ?string;

	//--------------------------------------------------------------------

	/**
	 * Automatically hashes the password when set.
	 *
	 * @see https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
	 *
	 * @param string $password
	 */
	public function setPassword(string $password): void;

	//--------------------------------------------------------------------

	/**
	 * Returns the user's email address.
	 *
	 * @return null|string Email address or null
	 */
	public function getEmail(): ?string;

	//--------------------------------------------------------------------

	/**
	 * Force a user to reset their password on next page refresh
	 * or login. Checked in the LocalAuthenticator's check() method.
	 *
	 * @param User $user
	 *
	 * @throws \Exception
	 *
	 * @return mixed Self
	 */
	public function forcePasswordReset();

	//--------------------------------------------------------------------

	/**
	 * Generates a secure hash to use for password reset purposes,
	 * saves it to the instance.
	 *
	 * @throws \Exception
	 *
	 * @return mixed Self
	 */
	public function generateResetHash();

	//--------------------------------------------------------------------

	/**
	 * Generates a secure random hash to use for account activation.
	 *
	 * @throws \Exception
	 *
	 * @return mixed Self
	 */
	public function generateActivateHash();

	//--------------------------------------------------------------------

	/**
	 * Activate user.
	 *
	 * @return mixed Self
	 */
	public function activate();

	//--------------------------------------------------------------------

	/**
	 * Unactivate user.
	 *
	 * @return mixed Self
	 */
	public function deactivate();

	//--------------------------------------------------------------------

	/**
	 * Checks to see if a user is active.
	 *
	 * @return bool
	 */
	public function isActive(): bool;

	//--------------------------------------------------------------------

	/**
	 * Bans a user.
	 *
	 * @param string    $reason    Reason for the ban
	 * @param null|bool $permanent Is it permanent?
	 *
	 * @return mixed Self
	 */
	public function ban(string $reason, bool $permanent = false);

	//--------------------------------------------------------------------

	/**
	 * Removes a ban from a user.
	 *
	 * @return mixed Self
	 */
	public function unBan();

	//--------------------------------------------------------------------

	/**
	 * Checks to see if a user has been banned.
	 *
	 * @return bool
	 */
	public function isBanned(): bool;

	//--------------------------------------------------------------------

	/**
	 * Checks whether a user has to change the password.
	 *
	 * @return bool
	 */
	public function isPasswordChangeForced(): bool;

	//--------------------------------------------------------------------

	/**
	 * Determines whether the user has the appropriate permission,
	 * either directly, or through one of it's groups.
	 *
	 * @param string $permission
	 *
	 * @return bool
	 */
	public function can(string $permission): bool;

	//--------------------------------------------------------------------

	/**
	 * Returns the user's permissions, formatted for simple checking:.
	 *
	 * [
	 *    id => name,
	 *    id=> name,
	 * ]
	 *
	 * @throws \Exception
	 *
	 * @return array
	 */
	public function getPermissions(): array;

	//--------------------------------------------------------------------

	/**
	 * Returns the user's roles, formatted for simple checking.
	 *
	 * [
	 *    id => name,
	 *    id => name,
	 * ]
	 *
	 * @throws \Exception
	 *
	 * @return array
	 */
	public function getRoles(): array;

	//--------------------------------------------------------------------

	/**
	 * Automatically converts the status name to ID.
	 *
	 * @param string $status Status name
	 */
	public function setStatus(string $status): void;

	//--------------------------------------------------------------------

	/**
	 * Returns the user's status.
	 *
	 * @return null|string Status name or null
	 */
	public function getStatus(): ?string;

	//--------------------------------------------------------------------

	/**
	 * Returns the user's activation token hash.
	 *
	 * @return null|string Token hash or null
	 */
	public function getActivateToken(): ?string;

	//--------------------------------------------------------------------

	/**
	 * Returns the user's password reset token hash.
	 *
	 * @return null|string Password reset token hash or null
	 */
	public function getPasswordResetToken(): ?string;
}
