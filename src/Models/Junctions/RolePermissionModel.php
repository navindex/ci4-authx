<?php

namespace Navindex\AuthX\Models\Junctions;

use Navindex\AuthX\Entities\RolePermission;
use Navindex\AuthX\Models\Base\JunctionModel;

class RolePermissionModel extends JunctionModel
{
	protected $table = 'role_permission';

	protected $tables = ['role', 'permission'];

	protected $returnType = RolePermission::class;
}
