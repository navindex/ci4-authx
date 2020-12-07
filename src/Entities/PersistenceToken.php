<?php

namespace Navindex\AuthX\Entities;

use Navindex\AuthX\Entities\BaseEntity;

class PersistenceToken extends BaseEntity
{
	/**
	 * Array of field names and the type of value to cast them as
	 * when they are accessed.
	 *
	 * @var array
	 */
	protected $casts = [
		'id'             => 'integer',
		'user_id'        => 'integer',
		'selector'       => 'string',
		'validator_hash' => 'string',
		'expires_at'     => 'datetime',
		'deleted'        => 'boolean',
		'creator_id'     => 'integer',
	];
}
