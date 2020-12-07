<?php

namespace Navindex\AuthX\Models\Interfaces;

interface TokenModelInterface
{
	/**
	 * Stores a remember-me token for the user.
	 *
	 * @param int    $userID    User ID
	 * @param string $selector  Device selector
	 * @param string $validator Validator hash
	 * @param string $expires   Expiry date and time (UTC)
	 *
	 * @return false|int Token ID or false
	 */
	public function rememberUser(int $userID, string $selector, string $validator, string $expires);

	//--------------------------------------------------------------------

	/**
	 * Returns the remember-me token info for a given selector.
	 *
	 * @param string $selector Device selector
	 *
	 * @return null|object Token entity
	 */
	public function getToken(string $selector);

	//--------------------------------------------------------------------

	/**
	 * Updates the validator for a given selector.
	 *
	 * @param string $selector  Device selector
	 * @param string $validator Validator hash
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function updateValidator(string $selector, string $validator): bool;

	//--------------------------------------------------------------------

	/**
	 * Removes all persistent login tokens (remember-me) for a single user
	 * across all devices they may have logged in with.
	 *
	 * @param int $userID User ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function purgeUserTokens(int $userId): bool;

	//--------------------------------------------------------------------

	/**
	 * Purges any records that are past
	 * their expiration date already.
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function purgeExpiredTokens(): bool;
}
