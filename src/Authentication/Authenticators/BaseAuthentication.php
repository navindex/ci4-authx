<?php

namespace Navindex\AuthX\Authentication\Authenticators;

use Navindex\AuthX\Config\Auth;

class BaseAuthentication
{
	/**
	 * Configuration settings.
	 *
	 * @var \Navindex\AuthX\Config\Auth
	 */
	protected $config;

	/**
	 * Error message.
	 *
	 * @var string
	 */
	protected $error;

	/**
	 * User entity.
	 *
	 * @var object
	 */
	protected $user;

	//--------------------------------------------------------------------

	/**
	 * Constructor.
	 *
	 * @param \Navindex\AuthX\Config\Auth $config Configuration settings
	 */
	public function __construct(Auth $config)
	{
		$this->config = $config;
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

	//--------------------------------------------------------------------

	/**
	 * Whether to continue instead of throwing exceptions,
	 * as defined in config.
	 *
	 * @return bool True if silent mode is on, flase otherwise
	 */
	public function silent(): bool
	{
		return $this->config->silent ?? false;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the User instance for the current logged in user.
	 *
	 * @return null|object User entity or null
	 */
	public function user(): ?object
	{
		return $this->user;
	}
}
