<?php

namespace Navindex\Auth\Models\Base;

use Navindex\Auth\Models\Base\BaseModel;

abstract class ListModel extends BaseModel
{
	protected $primaryKey = 'id';

	protected $uniqueKeys = ['name'];

	protected $returnType = 'array';

	protected $allowedFields = [];

	protected $parsableFields = [];

	protected $validationRules = [];

	protected $afterFind = ['parse', 'processResultAfterFind'];

	protected $useSoftDeletes = false;
}
