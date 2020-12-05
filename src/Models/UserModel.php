<?php

namespace Navindex\Auth\Models;

use Navindex\Auth\Entities\Attempt;
use Navindex\Auth\Entities\User;
use Navindex\Auth\Models\AttemptModel;
use Navindex\Auth\Models\Base\BaseModel;
use Navindex\Auth\Models\Junctions\UserPermissionModel;
use Navindex\Auth\Models\Junctions\UserRoleModel;
use Navindex\Auth\Models\Lists\UserFullPermissionListModel;
use Navindex\Auth\Models\Lists\UserPermissionListModel;
use Navindex\Auth\Models\Lists\UserRoleListModel;
use Navindex\Auth\Models\Types\PermissionModel;
use Navindex\Auth\Models\Types\RoleModel;
use CodeIgniter\Database\BaseBuilder;
use Navindex\Auth\Models\UserModelInterface;

class UserModel extends BaseModel implements UserModelInterface
{
	protected $table = 'user';
	protected $primaryKey = 'id';
	protected $uniqueKeys = ['username'];
	protected $returnType = User::class;
	protected $allowedFields = [
		'email',
		'username',
		'email',
		'password_hash',
		'reset_hash',
		'reset_at',
		'reset_expires',
		'activate_hash',
		'force_pass_reset',
		'status_id',
		'status_reason',
		'deleted',
		'creator_id',
	];
	protected $validationRules = [
		'username'      => 'alpha_numeric_punct|min_length[3]|is_unique[user.username,id,{id}]',
		'email'         => 'valid_email|is_unique[user.email,id,{id}]',
		'password_hash' => 'required',
	];
	protected $afterInsert = ['assignDefaultRole'];

	/**
	 * The id of a role to assign.
	 * Set internally by withRole.
	 * @var int
	 */
	protected $assignRole;

	//--------------------------------------------------------------------
	// User
	//--------------------------------------------------------------------

	/**
	 * Retrieves all available users.
	 *
	 * @return array Array of user objects
	 */
	public function fetchAll(): array
	{
		return $this->findAll();
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves a single user.
	 *
	 * @param int|string $user User ID or name
	 *
	 * @return null|\App\Entities\User User object or null
	 */
	public function fetch($user): ?User
	{
		if (is_numeric($user)) {
			return $this->find($user);
		}

		if (is_string($user)) {
			return $this->findUnique('username', $user);
		}

		return null;
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves a user by its credentials.
	 *
	 * @param array $credentials Associative array of credentials
	 *
	 * @return null|\App\Entities\User User record or null
	 */
	public function fetchByCredentials(array $credentials): ?User
	{
		return $this->where($credentials)->first();
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves the user ID.
	 *
	 * @param int|string $user User ID or name
	 *
	 * @return null|int User ID or null
	 */
	public function getId($user): ?int
	{
		return ($this->fetch($user))->id ?? null;
	}

	//--------------------------------------------------------------------
	/**
	 * Retrieves the user name.
	 *
	 * @param int|string $user User ID or name
	 *
	 * @return null|string User name or null
	 */
	public function getName($user): ?string
	{
		return ($this->fetch($user))->username ?? null;
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a user.
	 *
	 * @param int|string $user User ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function remove($user): bool
	{
		$userId = $this->getId($user);

		if (is_numeric($userId)) {
			return $this->delete($userId);
		}

		return false;
	}

	//--------------------------------------------------------------------

	/**
	 * Convenience function to filter inactive users.
	 *
	 * @return \App\Models\UserModel
	 */
	public function inactive(): self
	{
		return $this->whereIn('status_id', function (BaseBuilder $builder) {
			return $builder->select('id')->from('user_status')->whereIn('name', ['registered', 'inactive']);
		});
	}

	//--------------------------------------------------------------------
	// Role
	//--------------------------------------------------------------------

	/**
	 * Retrieves all roles of a single user.
	 *
	 * @param int|string $user User ID or username
	 *
	 * @return array Array of role objects
	 */
	public function getRoles($user): array
	{
		$userId = $this->getId($user);

		if (!is_numeric($userId)) {
			return [];
		}
		return model(UserRoleListModel::class)->getUserRoles($userId);
	}

	//--------------------------------------------------------------------

	/**
	 * Checks whether a specific role has a specific permission.
	 *
	 * @param int|string $user User ID or username
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the user has the role, false otherwise
	 */
	public function hasRole($user, $role): bool
	{
		$userId = $this->getId($user);

		if (!is_numeric($userId)) {
			return false;
		}

		$roleId = model(RoleModel::class)->getId($role);

		if (!is_numeric($roleId)) {
			return false;
		}

		return !empty(model(UserRoleListModel::class)->find([$userId, $roleId]));
	}

	//--------------------------------------------------------------------

	/**
	 * Adds a user role.
	 *
	 * @param int|string $user User ID or username
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addRole($user, $role): bool
	{
		$userId = $this->getId($user);

		if (!is_numeric($userId)) {
			return false;
		}

		$roleId = model(RoleModel::class)->getId($role);

		if (!is_numeric($roleId)) {
			return false;
		}

		return model(UserRoleModel::class)->connect($userId, $roleId);
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a user role.
	 *
	 * @param int|string $user User ID or username
	 * @param int|string $role Role ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeRole($user, $role): bool
	{
		$userId = $this->getId($user);

		if (!is_numeric($userId)) {
			return false;
		}

		$roleId = model(RoleModel::class)->getId($role);

		if (!is_numeric($roleId)) {
			return false;
		}

		return model(UserRoleModel::class)->disconnect($userId, $roleId);
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a single user from all roles.
	 *
	 * @param int|string $user User ID or username
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeAllRoles($user): bool
	{
		$userId = $this->getId($user);

		if (!is_numeric($userId)) {
			return false;
		}

		return model(UserRoleModel::class)->deleteLeft($userId);
	}

	//--------------------------------------------------------------------
	// Permission
	//--------------------------------------------------------------------

	/**
	 * Retrieves all permissions of a single user.
	 *
	 * @param int|string $user   User ID or username
	 * @param null|bool  $direct True: direct user permissions only
	 *                           False: combination of user and role permissions
	 *
	 * @return array Array of permission objects
	 */
	public function getPermissions($user, bool $direct = false): array
	{
		$userId = $this->getId($user);

		if (!is_numeric($userId)) {
			return [];
		}

		$modelClass = $direct
			? UserPermissionListModel::class
			: UserFullPermissionListModel::class;

		return model($modelClass)->getUserPermissions($userId);
	}

	//--------------------------------------------------------------------

	/**
	 * Checks whether a specific permission has a specific permission.
	 *
	 * @param int|string $user         User ID or username
	 * @param int|string $permission   Permission ID or name
	 * @param null|bool  $direct       True: direct user permissions only
	 *                                 False: both user and role permissions
	 *
	 * @return bool True if the user has the permission, false otherwise
	 */
	public function hasPermission($user, $permission, bool $direct = false): bool
	{
		$userId = $this->getId($user);

		if (!is_numeric($userId)) {
			return false;
		}

		$permissionId = model(PermissionModel::class)->getId($permission);

		if (!is_numeric($permissionId)) {
			return false;
		}

		$modelClass = $direct
			? UserPermissionListModel::class
			: UserFullPermissionListModel::class;

		return model($modelClass)->hasUserPermission($userId, $permissionId);
	}

	//--------------------------------------------------------------------

	/**
	 * Adds a permission directly to a user.
	 *
	 * @param int|string $user       User ID or username
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function addPermission($user, $permission): bool
	{
		$userId = $this->getId($user);

		if (!is_numeric($userId)) {
			return false;
		}

		$permissionId = model(PermissionModel::class)->getId($permission);

		if (!is_numeric($permissionId)) {
			return false;
		}

		return model(UserPermissionModel::class)->connect($userId, $permissionId);
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a direct permission from the user.
	 *
	 * @param int|string $user       User ID or username
	 * @param int|string $permission Permission ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removePermission($user, $permission): bool
	{
		$userId = $this->getId($user);

		if (!is_numeric($userId)) {
			return false;
		}

		$permissionId = model(PermissionModel::class)->getId($permission);

		if (!is_numeric($permissionId)) {
			return false;
		}

		return model(UserPermissionModel::class)->disconnect($userId, $permissionId);
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a single user from all permissions.
	 *
	 * @param int|string $user User ID or username
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function removeAllPermissions($user): bool
	{
		$userId = $this->getId($user);

		if (!is_numeric($userId)) {
			return false;
		}

		return model(UserPermissionModel::class)->deleteLeft($userId);
	}

	//--------------------------------------------------------------------
	// Attempt
	//--------------------------------------------------------------------

	/**
	 * Logs a login attempt for posterity sake.
	 *
	 * @param string      $email     Email address
	 * @param bool        $success   Was the login successful?
	 * @param int|null    $userId    User ID
	 * @param string|null $ipAddress IP address
	 * @param string|null $userAgent User agent
	 */
	public function logLoginAttempt(
		string $email,
		bool $success,
		int $userID = null,
		string $ipAddress = null,
		string $userAgent = null
	): void {
		$data = [
			'type'        => 'login',
			'email'       => $email,
			'user_id'     => $userID,
			'ipv4'        => filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null,
			'ipv6'        => filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null,
			'user_agent'  => $userAgent,
			'success'     => (int)$success,
			'captured_at' => gmdate('Y-m-d H:i:s')
		];

		model(AttemptModel::class)->insert(new Attempt($data));
	}

	//--------------------------------------------------------------------

	/**
	 * Logs a password reset attempt for posterity sake.
	 *
	 * @param string      $email     Email address
	 * @param string|null $token     Password reset token
	 * @param string|null $ipAddress IP address
	 * @param string|null $userAgent User agent
	 */
	public function logResetAttempt(
		string $email,
		string $token = null,
		string $ipAddress = null,
		string $userAgent = null
	): void {
		$data = [
			'type'        => 'reset',
			'email'       => $email,
			'ipv4'        => filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null,
			'ipv6'        => filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null,
			'user_agent'  => $userAgent,
			'token'       => $token,
			'captured_at' => gmdate('Y-m-d H:i:s')
		];

		model(AttemptModel::class)->insert(new Attempt($data));
	}

	//--------------------------------------------------------------------

	/**
	 * Logs an activation attempt for posterity sake.
	 *
	 * @param string|null $token     Password reset token
	 * @param string|null $ipAddress IP address
	 * @param string|null $userAgent User agent
	 */
	public function logActivationAttempt(
		string $token = null,
		string $ipAddress = null,
		string $userAgent = null
	): void {
		$data = [
			'type'        => 'activation',
			'ipv4'        => filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null,
			'ipv6'        => filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null,
			'user_agent'  => $userAgent,
			'token'       => $token,
			'captured_at' => gmdate('Y-m-d H:i:s')
		];

		model(AttemptModel::class)->insert(new Attempt($data));
	}

	//--------------------------------------------------------------------
	// Default role
	//--------------------------------------------------------------------

	/**
	 * Sets the role to assign any users created.
	 *
	 * @param int|string $role Role ID or name
	 *
	 * @return \App\Models\UserModel
	 */
	public function setDefaultRole($role): self
	{
		$this->assignRole = model(RoleModel::class)->getId($role);

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Clears the role to assign to newly created users.
	 *
	 * @return \App\Models\UserModel
	 */
	public function unsetDefaultRole(): self
	{
		$this->assignRole = null;

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * If a default role is assigned in Config\Auth, will
	 * add this user to that group. Will do nothing
	 * if the group cannot be found.
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	protected function assignDefaultRole($data)
	{
		if (is_numeric($this->assignRole)) {
			model(UserRoleModel::class)->connect($data['id'], $this->assignRole);
		}

		return $data;
	}
}
