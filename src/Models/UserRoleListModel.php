<?php

namespace App\Models\Lists;

use App\Models\Base\ListModel;

class UserRoleListModel extends ListModel
{
	protected $table = 'user_role_list';

	protected $primaryKey = ['user_id', 'role_id'];

	protected $afterFind = [];

	//--------------------------------------------------------------------

	/**
	 * Retrieves all users of a role.
	 *
	 * @param int $roleId Role ID
	 *
	 * @return array Array of user objects
	 */
	public function getRoleUsers(int $roleId): array
	{
		return $this->asObject()->select('user_id AS id, username AS name')->where('role_id', $roleId)->findAll();
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves all roles of a user.
	 *
	 * @param int $userId User ID
	 *
	 * @return array Array of role objects
	 */
	public function getUserRoles(int $userId): array
	{
		return $this->asObject()->select('role_id AS id, name')->where('user_id', $userId)->findAll();
	}
}
