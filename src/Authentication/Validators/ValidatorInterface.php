<?php

namespace Navindex\Auth\Authentication\Validators;

/**
 * Interface ValidatorInterface.
 */
interface ValidatorInterface
{
	/**
	 * Checks the password and returns true/false
	 * if it passes muster. Must return either true/false.
	 * True means the password passes this test and
	 * the password will be passed to any remaining validators.
	 * False will immediately stop validation process.
	 *
	 * @param string $password Password
	 * @param object $user     User object
	 *
	 * @return bool True if the password passed the test
	 */
	public function check(string $password, object $user = null): bool;

	//--------------------------------------------------------------------

	/**
	 * Returns the error string that should be displayed to the user.
	 *
	 * @return null|string Error message or null
	 */
	public function error(): ?string;

	//--------------------------------------------------------------------

	/**
	 * Returns a suggestion that may be displayed to the user
	 * to help them choose a better password. The method is
	 * required, but a suggestion is optional. May return
	 * an empty string instead.
	 *
	 * @return null|string Suggestion message or null
	 */
	public function suggestion(): ?string;
}
