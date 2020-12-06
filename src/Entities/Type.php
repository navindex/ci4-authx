<?php

namespace Navindex\Auth\Entities;

use Navindex\Auth\Entities\BaseEntity;

class Type extends BaseEntity
{
	/**
	 * Array of field names and the type of value to cast them as
	 * when they are accessed.
	 *
	 * @var array
	 */
	protected $casts = [
		'id'         => 'integer',
		'name'       => 'string',
		'label'      => 'string',
		'deleted'    => 'boolean',
		'creator_id' => 'integer',
	];
}
