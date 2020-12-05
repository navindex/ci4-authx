<?php

namespace App\Models\Junctions;

use App\Entities\UserRole;
use App\Models\Base\JunctionModel;

class UserRoleModel extends JunctionModel
{
	protected $table = 'user_role';
	protected $tables = ['user', 'role'];
    protected $returnType = UserRole::class;
}
