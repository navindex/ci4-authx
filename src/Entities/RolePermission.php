<?php

namespace Navindex\Auth\Entities;

use Navindex\Auth\Entities\BaseEntity;

class RolePermission extends BaseEntity
{
	/**
	 * Array of field names and the type of value to cast them as
	 * when they are accessed.
	 *
	 * @var array
	 */
	protected $casts = [
		'role_id'       => 'integer',
		'permission_id' => 'integer',
		'deleted'       => 'boolean',
		'creator_id'    => 'integer',
	];
}
