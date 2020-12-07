<?php

namespace Navindex\AuthX\Models\Junctions;

use Navindex\AuthX\Entities\UserRole;
use Navindex\AuthX\Models\Base\JunctionModel;

class UserRoleModel extends JunctionModel
{
	protected $table = 'user_role';

	protected $tables = ['user', 'role'];

	protected $returnType = UserRole::class;
}
