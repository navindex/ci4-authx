<?php

namespace Navindex\Auth\Authentication\Validators;

use Navindex\Auth\Config\Auth;

abstract class BaseValidator
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

	/**
	 * Suggestion message.
	 *
	 * @var string
	 */
	protected $suggestion;

	//--------------------------------------------------------------------

	/**
	 * Allows for setting a config file on the Validator.
	 *
	 * @param \Navindex\Auth\Config\Auth $config Configuration settings
	 *
	 * @return \Navindex\Auth\Authentication\Validators\BaseValidator
	 */
	public function setConfig(Auth $config): self
	{
		$this->config = $config;

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the error string that should be displayed to the user.
	 *
	 * @return null|string Error message or null
	 */
	public function error(): ?string
	{
		return $this->error;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns a suggestion that may be displayed to the user
	 * to help them choose a better password. The method is
	 * required, but a suggestion is optional. May return
	 * an empty string instead.
	 *
	 * @return null|string Suggestion message or null
	 */
	public function suggestion(): ?string
	{
		return $this->suggestion;
	}
}
