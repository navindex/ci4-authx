<?php

namespace Navindex\Auth\Models\Lists;

use Navindex\Auth\Models\Base\ListModel;

class UserPermissionListModel extends ListModel
{
	protected $table = 'user_permission_list';

	protected $primaryKey = ['user_id', 'permission_id'];

	protected $afterFind = [];

	//--------------------------------------------------------------------

	/**
	 * Retrieves all users directly having a specific permission.
	 *
	 * @param int $permissionId Permission ID
	 *
	 * @return array Array of user objects
	 */
	public function getPermissionUsers(int $permissionId): array
	{
		return $this->asObject()->select('user_id AS id, username AS name')->where('permission_id', $permissionId)->findAll();
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves all direct permissions of a user.
	 *
	 * @param int $userId User ID
	 *
	 * @return array Array of permission objects
	 */
	public function getUserPermissions(int $userId): array
	{
		return $this->asObject()->select('permission_id AS id, name')->where('user_id', $userId)->findAll();
	}
}
