<?php

namespace Navindex\Auth\Authentication\Validators;

use Navindex\Auth\Authentication\Validators\BaseValidator;
use Navindex\Auth\Authentication\Validators\ValidatorInterface;
use Navindex\Auth\Entities\UserInterface;

/**
 * Class NothingPersonalValidator.
 *
 * Checks password does not contain any personal information
 */
class NothingPersonalValidator extends BaseValidator implements ValidatorInterface
{
	/**
	 * Returns true if $password contains no part of the username
	 * or the user's email. Otherwise, it returns false.
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
		$password = \strtolower($password);

		if ($valid = true === $this->isNotPersonal($password, $user)) {
			$valid = $this->isNotSimilar($password, $user);
		}

		return $valid;
	}

	//--------------------------------------------------------------------

	/**
	 * isNotPersonal().
	 *
	 * Looks for personal information in a password. The personal info used
	 * comes from Navindex\Auth\Entities\User properties username and email.
	 *
	 * It is possible to include other fields as information sources.
	 * For instance, a project might require adding `firstname` and `lastname` properties
	 * to an extended version of the User class.
	 * The new fields can be included in personal information testing in by setting
	 * the `$personalFields` property in Navindex\Auth\Config\Auth, e.g.
	 *
	 *      public $personalFields = ['firstname', 'lastname'];
	 *
	 * isNotPersonal() returns true if no personal information can be found, or false
	 * if such info is found.
	 *
	 * @param string $password Password
	 * @param object $user     User object
	 *
	 * @return bool True if the password passed the test
	 */
	protected function isNotPersonal(string $password, object $user): bool
	{
		$userName = \strtolower($user->username);
		$email = \strtolower($user->email);
		$valid = true;

		// The most obvious transgressions
		if (
			$password === $userName ||
			$password === $email ||
			$password === \strrev($userName)
		) {
			$valid = false;
		}

		// Parse out as many pieces as possible from username, password and email.
		// Use the pieces as needles and haystacks and look every which way for matches.
		if ($valid) {
			// Take username apart for use as search needles
			$needles = $this->strip_explode($userName);

			// extract local-part and domain parts from email as separate needles
			[$localPart, $domain] = \explode('@', $email);
			// might be john.doe@example.com and we want all the needles we can get
			$emailParts = $this->strip_explode($localPart);
			if (!empty($domain)) {
				$emailParts[] = $domain;
			}
			$needles = \array_merge($needles, $emailParts);

			// Get any other "personal" fields defined in config
			$personalFields = $this->config->personalFields;
			if (!empty($personalFields)) {
				foreach ($personalFields as $value) {
					if (!empty($user->{$value})) {
						$needles[] = \strtolower($user->{$value});
					}
				}
			}

			$trivial = [
				'a', 'an', 'and', 'as', 'at', 'but', 'for',
				'if', 'in', 'not', 'of', 'or', 'so', 'the', 'then',
			];

			// Make password into haystacks
			$haystacks = $this->strip_explode($password);

			foreach ($haystacks as $haystack) {
				if (empty($haystack) || \in_array($haystack, $trivial)) {
					continue;  //ignore trivial words
				}

				foreach ($needles as $needle) {
					if (empty($needle) || \in_array($needle, $trivial)) {
						continue;
					}

					// look both ways in case password is subset of needle
					if (
						false !== \strpos($haystack, $needle) ||
						false !== \strpos($needle, $haystack)
					) {
						$valid = false;

						break 2;
					}
				}
			}
		}
		if ($valid) {
			return true;
		}

		$this->error = lang('Auth.errorPasswordPersonal');
		$this->suggestion = lang('Auth.suggestPasswordPersonal');

		return false;
	}

	//--------------------------------------------------------------------

	/**
	 * notSimilar() uses $password and $userName to calculate a similarity value.
	 * Similarity values equal to, or greater than Navindex\Auth\Config::maxSimilarity
	 * are rejected for being too much alike and false is returned.
	 * Otherwise, true is returned,.
	 *
	 * A $maxSimilarity value of 0 (zero) returns true without making a comparison.
	 * In other words, 0 (zero) turns off similarity testing.
	 *
	 * @param string $password Password
	 * @param object $user     User object
	 *
	 * @return bool True if the password passed the test
	 */
	protected function isNotSimilar(string $password, object $user): bool
	{
		$maxSimilarity = (float) $this->config->maxSimilarity;
		// sanity checking - working range 1-100, 0 is off
		if ($maxSimilarity < 1) {
			$maxSimilarity = 0;
		} elseif ($maxSimilarity > 100) {
			$maxSimilarity = 100;
		}

		if (!empty($maxSimilarity)) {
			$userName = \strtolower($user->username);

			\similar_text($password, $userName, $similarity);
			if ($similarity >= $maxSimilarity) {
				$this->error = lang('Auth.errorPasswordTooSimilar');
				$this->suggestion = lang('Auth.suggestPasswordTooSimilar');

				return false;
			}
		}

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * strip_explode($str).
	 *
	 * Replaces all non-word characters and underscores in $str with a space.
	 * Then it explodes that result using the space for a delimiter.
	 *
	 * @param string $str Input string
	 *
	 * @return array The exploded string
	 */
	protected function strip_explode($str): array
	{
		$stripped = \preg_replace('/[\W_]+/', ' ', $str);
		$parts = \explode(' ', \trim($stripped));

		// If it's not already there put the untouched input at the top of the array
		if (!\in_array($str, $parts)) {
			\array_unshift($parts, $str);
		}

		return $parts;
	}
}
