<?php

namespace Navindex\Auth\Authentication\Resetters;

use Navindex\Auth\Entities\UserInterface;

/**
 * Interface ResetterInterface.
 */
interface ResetterInterface
{
	/**
	 * Send reset message to user.
	 *
	 * @param \Navindex\Auth\Entities\UserInterface $user User record
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
