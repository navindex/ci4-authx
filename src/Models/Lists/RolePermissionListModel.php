<?php

namespace Navindex\AuthX\Models\Lists;

use Navindex\AuthX\Models\Base\ListModel;

class RolePermissionListModel extends ListModel
{
	protected $table = 'role_permission_list';

	protected $primaryKey = ['role_id', 'permission_id'];

	protected $afterFind = [];

	//--------------------------------------------------------------------

	/**
	 * Retrieves all permissions of a role.
	 *
	 * @param int $roleId Role ID
	 *
	 * @return array Array of permission objects
	 */
	public function getRolePermissions(int $roleId): array
	{
		return $this->asObject()->select('permission_id AS id, name')->where('role_id', $roleId)->findAll();
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves all roles having a specific permission.
	 *
	 * @param int $permissionId Permission ID
	 *
	 * @return array Array of role objects
	 */
	public function getPermissionRoles(int $permissionId): array
	{
		return $this->asObject()->select('role_id AS id, role AS name')->where('permission_id', $permissionId)->findAll();
	}
}
