<?php

namespace Navindex\Auth\Test\Fakers;

use Faker\Generator;
use Navindex\Auth\Entities\User;
use Navindex\Auth\Models\UserModel;

class UserFaker extends UserModel
{
	/**
	 * Faked data for Fabricator.
	 *
	 * @param Generator $faker
	 *
	 * @return User
	 */
	public function fake(Generator &$faker): User
	{
		return new User([
			'email'    => $faker->email,
			'username' => implode('_', $faker->words),
			'password' => bin2hex(random_bytes(16)),
		]);
	}
}
