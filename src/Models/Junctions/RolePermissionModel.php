<?php

namespace Navindex\Auth\Models\Junctions;

use Navindex\Auth\Entities\RolePermission;
use Navindex\Auth\Models\Base\JunctionModel;

class RolePermissionModel extends JunctionModel
{
	protected $table = 'role_permission';
	protected $tables = ['role', 'permission'];
	protected $returnType = RolePermission::class;
}
