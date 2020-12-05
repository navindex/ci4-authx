<?php

namespace Navindex\Auth\Models\Lists;

use Navindex\Auth\Models\Base\ListModel;

class UserFullPermissionListModel extends ListModel
{
	protected $table = 'user_full_permission_list';

	protected $primaryKey = ['user_id', 'permission_id'];

	protected $afterFind = [];

	//--------------------------------------------------------------------

	/**
	 * Retrieves all permissions of a user, including role permissions.
	 *
	 * @param int $userId User ID
	 *
	 * @return array Associative permission array (id => name)
	 */
	public function getUserPermissions(int $userId): array
	{
		$result = $this->where('user_id', $userId)->findAll();

		$permissions = [];
		foreach ($result as $record) {
			$permissions[$record['permission_id']] = strtolower($record['name']);
		}

		return $permissions;
	}

	//--------------------------------------------------------------------

	/**
	 * Checks to see if a user, or one of their groups,
	 * has a specific permission.
	 *
	 * @param $userId       User ID
	 * @param $permissionId Permission ID
	 *
	 * @return bool
	 */
	public function hasUserPermission(int $userId, int $permissionId): bool
	{
		return !empty($this->find([$userId, $permissionId]));
	}
}
