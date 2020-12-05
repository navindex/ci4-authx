<?php

namespace App\Models\Junctions;

use App\Entities\RolePermission;
use App\Models\Base\JunctionModel;

class RolePermissionModel extends JunctionModel
{
	protected $table = 'role_permission';
	protected $tables = ['role', 'permission'];
	protected $returnType = RolePermission::class;
}
