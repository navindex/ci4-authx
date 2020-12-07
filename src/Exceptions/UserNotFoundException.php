<?php

namespace Navindex\AuthX\Exceptions;

class UserNotFoundException extends \RuntimeException
{
	public static function forUserID(int $id)
	{
		return new self(lang('Auth.exception.userNotFound', [$id]), 404);
	}
}
