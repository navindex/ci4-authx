<?php

namespace Navindex\AuthX\Authentication\Authenticators;

interface AuthenticatorInterface
{
	/**
	 * Attempts to validate the credentials and log a user in.
	 *
	 * @param array $credentials User credentials
	 * @param bool  $remember    Should we remember the user (if enabled)
	 *
	 * @return bool True for successful validation and login, false otherwise
	 */
	public function attempt(array $credentials, bool $remember = null): bool;

	//--------------------------------------------------------------------

	/**
	 * Checks to see if the user is logged in or not.
	 *
	 * @return bool True if the user is logged in
	 */
	public function check(): bool;

	//--------------------------------------------------------------------

	/**
	 * Checks the user's credentials to see if they could authenticate.
	 * Unlike `attempt()`, will not log the user into the system.
	 *
	 * @param array $credentials User credentials
	 * @param bool  $returnUser  Return the user record?
	 *
	 * @return bool|object Validation result or user entity
	 */
	public function validate(array $credentials, bool $returnUser = false);

	//--------------------------------------------------------------------

	/**
	 * Logs a user out of the system.
	 */
	public function logout(): void;

	//--------------------------------------------------------------------

	/**
	 * Returns the User instance for the current logged in user.
	 *
	 * @return null|object User entity or null
	 */
	public function user(): ?object;

	//--------------------------------------------------------------------

	/**
	 * Returns the error string that should be displayed to the user.
	 *
	 * @return null|string Error message or null
	 */
	public function error(): ?string;
}
