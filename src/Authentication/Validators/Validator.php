<?php

namespace Navindex\Auth\Authentication\Validators;

use App\Exceptions\AuthException;
use Navindex\Auth\Config\Auth;

class Validator
{
	/**
	 * Configuration settings.
	 *
	 * @var \Navindex\Auth\Config\Auth
	 */
	protected $config;

	/**
	 * Error message.
	 *
	 * @var string
	 */
	protected $error;

	//--------------------------------------------------------------------

	/**
	 * Constructor.
	 *
	 * @param \Navindex\Auth\Config\Auth $config Configuration settings
	 */
	public function __construct(Auth $config)
	{
		$this->config = $config;
	}

	//--------------------------------------------------------------------

	/**
	 * Checks a password against all of the Validators specified
	 * in `$passwordValidators` setting in Navindex\Auth\Config\Auth.php.
	 *
	 * @param string $password Password
	 * @param object $user     User object
	 *
	 * @return bool True if the password passed the test
	 */
	public function check(string $password, object $user = null): bool
	{
		if (is_null($user)) {
			throw AuthException::forNoEntityProvided();
		}

		$password = trim($password);

		if (empty($password)) {
			$this->error = lang('Auth.errorPasswordEmpty');

			return false;
		}

		$valid = false;

		foreach ($this->config->activeValidators as $className) {
			$class = new $className();
			$class->setConfig($this->config);

			if (false === $class->check($password, $user)) {
				$this->error = $class->error();
				$this->suggestion = $class->suggestion();

				$valid = false;

				break;
			}

			$valid = true;
		}

		return $valid;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the current error.
	 *
	 * @return null|string Error message or null
	 */
	public function error(): ?string
	{
		return $this->error;
	}
}
