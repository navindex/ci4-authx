<?php

namespace Navindex\AuthX\Authorisation;

class BaseAuthorisation
{
	/**
	 * Error message(s).
	 *
	 * @var array|string
	 */
	protected $error;

	//--------------------------------------------------------------------

	/**
	 * Returns the current error.
	 *
	 * @return null|array|string Error message(s) or null
	 */
	public function error()
	{
		return $this->error;
	}
}
