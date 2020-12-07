<?php

namespace Navindex\AuthX\Models\Junctions;

use Navindex\AuthX\Entities\UserPermission;
use Navindex\AuthX\Models\Base\JunctionModel;

class UserPermissionModel extends JunctionModel
{
	protected $table = 'user_permission';

	protected $tables = ['user', 'permission'];

	protected $returnType = UserPermission::class;
}
