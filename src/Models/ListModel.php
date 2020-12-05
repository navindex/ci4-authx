<?php

namespace App\Models\Base;

abstract class ListModel extends ParsableModel
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
