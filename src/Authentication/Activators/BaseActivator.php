<?php

namespace Navindex\Auth\Authentication\Activators;

use Navindex\Auth\Config\Auth;

abstract class BaseActivator
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
	 * Gets a config settings for current Activator.
	 *
	 * @return null|object Activator settings
	 */
	public function getSettings(): ?object
	{
		return (object) $this->config->activators[\get_class($this)];
	}
}
