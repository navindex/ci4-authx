<?php

namespace Navindex\AuthX\Entities;

use Navindex\AuthX\Entities\BaseEntity;

class UserPermission extends BaseEntity
{
	/**
	 * Array of field names and the type of value to cast them as
	 * when they are accessed.
	 *
	 * @var array
	 */
	protected $casts = [
		'user_id'       => 'integer',
		'permission_id' => 'integer',
		'deleted'       => 'boolean',
		'creator_id'    => 'integer',
	];
}
