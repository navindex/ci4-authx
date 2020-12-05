<?php

namespace Navindex\Auth\Authorisation;

class BaseAuthorisation
{
	/**
	 * Error message(s).
	 *
	 * @var string|array
	 */
	protected $error;

	//--------------------------------------------------------------------

	/**
	 * Returns the current error.
	 *
	 * @return null|string|array Error message(s) or null
	 */
	public function error()
	{
		return $this->error;
	}
}
