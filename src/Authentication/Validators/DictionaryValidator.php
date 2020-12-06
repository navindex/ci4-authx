<?php

namespace Navindex\Auth\Authentication\Validators;

use Navindex\Auth\Authentication\Validators\BaseValidator;
use Navindex\Auth\Authentication\Validators\ValidatorInterface;

/**
 * Class DictionaryValidator.
 *
 * Checks passwords against a list of 65k commonly used passwords
 * that was compiled by InfoSec.
 */
class DictionaryValidator extends BaseValidator implements ValidatorInterface
{
	/**
	 * Checks the password against the words in the file and returns false
	 * if a match is found. Returns true if no match is found.
	 * If true is returned the password will be passed to next validator.
	 * If false is returned the validation process will be immediately stopped.
	 *
	 * @param string $password Password
	 * @param object $user     User object
	 *
	 * @return bool True if the password passed the test
	 */
	public function check(string $password, object $user = null): bool
	{
		// Loop over our file
		$fp = \fopen(__DIR__ . '/_dictionary.txt', 'r');
		if ($fp) {
			while (false !== ($line = \fgets($fp, 4096))) {
				if ($password == \trim($line)) {
					\fclose($fp);

					$this->error = lang('Auth.errorPasswordCommon');
					$this->suggestion = lang('Auth.suggestPasswordCommon');

					return false;
				}
			}
		}

		\fclose($fp);

		return true;
	}
}
