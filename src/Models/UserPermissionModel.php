<?php

namespace App\Models\Junctions;

use App\Entities\UserPermission;
use App\Models\Base\JunctionModel;

class UserPermissionModel extends JunctionModel
{
	protected $table = 'user_permission';
	protected $tables = ['user', 'permission'];
	protected $returnType = UserPermission::class;
}
