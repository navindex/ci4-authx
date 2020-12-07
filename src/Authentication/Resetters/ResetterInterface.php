<?php

namespace Navindex\AuthX\Authentication\Resetters;

use Navindex\AuthX\Entities\UserInterface;

/**
 * Interface ResetterInterface.
 */
interface ResetterInterface
{
	/**
	 * Send reset message to user.
	 *
	 * @param \Navindex\AuthX\Entities\UserInterface $user User record
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function send(UserInterface $user = null): bool;

	//--------------------------------------------------------------------

	/**
	 * Returns the error string that should be displayed to the user.
	 *
	 * @return null|string Error message or null
	 */
	public function error(): ?string;
}
