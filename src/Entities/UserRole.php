<?php

namespace Navindex\Auth\Entities;

use Navindex\Auth\Entities\Base\BaseEntity;

class UserRole extends BaseEntity
{
    /**
     * Array of field names and the type of value to cast them as
     * when they are accessed.
     *
     * @var array
     */
    protected $casts = [
        'user_id'    => 'integer',
        'role_id'    => 'integer',
        'deleted'    => 'boolean',
        'creator_id' => 'integer',
    ];
}
