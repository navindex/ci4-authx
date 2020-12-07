<?php

namespace Navindex\AuthX\Test\Fakers;

use Faker\Generator;
use Navindex\AuthX\Authorization\PermissionModel;

class PermissionFaker extends PermissionModel
{
	/**
	 * Faked data for Fabricator.
	 *
	 * @param Generator $faker
	 *
	 * @return array
	 */
	public function fake(Generator &$faker): array
	{
		return [
			'name'        => $faker->word,
			'description' => $faker->sentence,
		];
	}
}
