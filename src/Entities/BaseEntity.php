<?php

namespace Navindex\AuthX\Entities;

use CodeIgniter\Entity;

abstract class BaseEntity extends Entity
{
	/**
	 * Holds the current values of all class vars.
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * Maps names used in sets and gets against unique
	 * names within the class, allowing independence from
	 * database column names.
	 *
	 * Example:
	 *  $datamap = [
	 *      'db_name' => 'class_name'
	 *  ];
	 *
	 * @var array
	 */
	protected $datamap = [];

	/**
	 * Define properties that are automatically converted to Time instances.
	 */
	protected $dates = [];

	/**
	 * Array of field names and the type of value to cast them as
	 * when they are accessed.
	 *
	 * @var array
	 */
	protected $casts = [];

	//--------------------------------------------------------------------

	/**
	 * Sets the record creator.
	 *
	 * @todo Need to apply authenticated user id
	 */
	public function setCreatorId()
	{
		$this->attributes['creator_id'] = 1;
	}
}
