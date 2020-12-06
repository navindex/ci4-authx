<?php

namespace Navindex\Auth\Models\Junctions;

use Navindex\Auth\Entities\UserRole;
use Navindex\Auth\Models\Base\JunctionModel;

class UserRoleModel extends JunctionModel
{
	protected $table = 'user_role';

	protected $tables = ['user', 'role'];

	protected $returnType = UserRole::class;
}
