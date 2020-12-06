<?php

namespace Navindex\Auth\Models;

use Navindex\Auth\Entities\Attempt;
use Navindex\Auth\Models\Base\BaseModel;

class AttemptModel extends BaseModel
{
	protected $table = 'attempt';

	protected $primaryKey = 'id';

	protected $uniqueKeys = [];

	protected $returnType = Attempt::class;

	protected $allowedFields = [
		'type',
		'captured_at',
		'success',
		'ipv4',
		'ipv6',
		'user_agent',
		'email',
		'user_id',
		'token',
		'deleted',
		'creator_id',
	];

	protected $validationRules = [
		'type'        => 'is_not_unique[attempt_type.name]',
		'captured_at' => 'permit_empty|valid_date',
		'success'     => 'permit_empty|in_list[0,1]',
		'email'       => 'permit_empty|valid_email',
		'user_id'     => 'permit_empty|is_not_unique[style.id]',
	];
}
