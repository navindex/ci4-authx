<?php

namespace Navindex\Auth\Models\Junctions;

use Navindex\Auth\Entities\UserPermission;
use Navindex\Auth\Models\Base\JunctionModel;

class UserPermissionModel extends JunctionModel
{
	protected $table = 'user_permission';
	protected $tables = ['user', 'permission'];
	protected $returnType = UserPermission::class;
}
