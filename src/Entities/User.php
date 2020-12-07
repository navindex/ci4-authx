<?php

namespace Navindex\AuthX\Entities;

use Navindex\AuthX\Entities\BaseEntity;
use Navindex\AuthX\Entities\UserInterface;
use Navindex\AuthX\Models\Lists\UserFullPermissionListModel;
use Navindex\AuthX\Models\Lists\UserRoleListModel;
use Navindex\AuthX\Models\Types\UserStatusModel;

class User extends BaseEntity implements UserInterface
{
	/**
	 * Define properties that are automatically converted to Time instances.
	 */
	protected $dates = ['reset_requested_at', 'reset_expires_at'];

	/**
	 * Array of field names and the type of value to cast them as
	 * when they are accessed.
	 */
	protected $casts = [
		'id'                 => 'integer',
		'username'           => 'string',
		'email'              => 'string',
		'password_hash'      => 'string',
		'reset_hash'         => '?string',
		'reset_requested_at' => '?datetime',
		'reset_expires_at'   => '?datetime',
		'activate_hash'      => '?string',
		'force_pass_reset'   => '?boolean',
		'status_id'          => 'integer',
		'status_reason'      => '?string',
		'deleted'            => 'boolean',
		'creator_id'         => 'integer',
	];

	/**
	 * Maps names used in sets and gets against unique
	 * names within the class, allowing independence from
	 * database column names.
	 *
	 * Example:
	 *  $datamap = [
	 *      'db_name' => 'class_name'
	 *  ];
	 *
	 * @var array
	 */
	protected $datamap = [
		'status' => 'status_id',
	];

	/**
	 * Per-user permissions cache.
	 *
	 * @var array
	 */
	protected $permissions = [];

	/**
	 * Per-user roles cache.
	 *
	 * @var array
	 */
	protected $roles = [];

	/**
	 * User status list.
	 * id => name.
	 *
	 * @var array
	 */
	protected $status = [];

	//--------------------------------------------------------------------

	/**
	 * Returns the user ID.
	 *
	 * @return null|int User ID or null
	 */
	public function getUserId(): ?int
	{
		return $this->attributes['id'] ?? null;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the user's password hash.
	 *
	 * @return null|string Password hash or null
	 */
	public function getPassword(): ?string
	{
		return $this->attributes['password_hash'] ?? null;
	}

	//--------------------------------------------------------------------

	/**
	 * Automatically hashes the password when set.
	 *
	 * @see https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
	 *
	 * @param string $password
	 */
	public function setPassword(string $password): void
	{
		$config = config('Auth');

		if (
			(\defined('PASSWORD_ARGON2I') && PASSWORD_ARGON2I == $config->hashAlgorithm) ||
			(\defined('PASSWORD_ARGON2ID') && PASSWORD_ARGON2ID == $config->hashAlgorithm)
		) {
			$hashOptions = [
				'memory_cost' => $config->hashMemoryCost,
				'time_cost'   => $config->hashTimeCost,
				'threads'     => $config->hashThreads,
			];
		} else {
			$hashOptions = [
				'cost' => $config->hashCost,
			];
		}

		$this->attributes['password_hash'] = \password_hash(
			\base64_encode(\hash('sha384', $password, true)),
			$config->hashAlgorithm,
			$hashOptions
		);

		/*
			Set these vars to null in case a reset password was asked.
			Scenario:
				user (a *dumb* one with short memory) requests a
				reset-token and then does nothing => asks the
				administrator to reset his password.
			User would have a new password but still anyone with the
			reset-token would be able to change the password.
		*/
		$this->attributes['reset_hash'] = null;
		$this->attributes['reset_requested_at'] = null;
		$this->attributes['reset_expires_at'] = null;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the user's status.
	 *
	 * @return null|string Status name or null
	 */
	public function getEmail(): ?string
	{
		return $this->attributes['email'] ?? null;
	}

	//--------------------------------------------------------------------

	/**
	 * Force a user to reset their password on next page refresh
	 * or login. Checked in the LocalAuthenticator's check() method.
	 *
	 * @param User $user
	 *
	 * @throws \Exception
	 *
	 * @return \App\Entities\User
	 */
	public function forcePasswordReset(): self
	{
		$this->generateResetHash();
		$this->attributes['force_pass_reset'] = 1;

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Generates a secure hash to use for password reset purposes,
	 * saves it to the instance.
	 *
	 * @throws \Exception
	 *
	 * @return \App\Entities\User
	 */
	public function generateResetHash(): self
	{
		$this->attributes['reset_hash'] = \bin2hex(\random_bytes(16));
		$this->attributes['reset_expires_at'] = \date('Y-m-d H:i:s', \time() + config('Auth')->resetTime);

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Generates a secure random hash to use for account activation.
	 *
	 * @throws \Exception
	 *
	 * @return \App\Entities\User
	 */
	public function generateActivateHash(): self
	{
		$this->setStatus('registered');
		$this->attributes['activate_hash'] = \bin2hex(\random_bytes(16));
		$this->attributes['status_reason'] = null;

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Activate user.
	 *
	 * @return \App\Entities\User
	 */
	public function activate(): self
	{
		$this->setStatus('active');
		$this->attributes['activate_hash'] = null;
		$this->attributes['status_reason'] = null;

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Unactivate user.
	 *
	 * @return \App\Entities\User
	 */
	public function deactivate(): self
	{
		$this->setStatus('inactive');
		$this->attributes['activate_hash'] = null;
		$this->attributes['status_reason'] = null;

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Checks to see if a user is active.
	 *
	 * @return bool
	 */
	public function isActive(): bool
	{
		return !\in_array($this->getStatus(), ['registered', 'inactive']);
	}

	//--------------------------------------------------------------------

	/**
	 * Bans a user.
	 *
	 * @param string    $reason    Reason for the ban
	 * @param null|bool $permanent Is it permanent?
	 *
	 * @return \App\Entities\User
	 */
	public function ban(string $reason, bool $permanent = false): self
	{
		$this->setStatus($permanent ? 'permabanned' : 'banned');
		$this->attributes['status_reason'] = $reason;

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a ban from a user.
	 *
	 * @return \App\Entities\User
	 */
	public function unBan(): self
	{
		if ('banned' === $this->getStatus()) {
			$this->setStatus('active');
			$this->attributes['status_reason'] = null;
		}

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Checks to see if a user has been banned.
	 *
	 * @return bool
	 */
	public function isBanned(): bool
	{
		return \in_array($this->getStatus(), ['banned', 'permabanned']);
	}

	//--------------------------------------------------------------------

	/**
	 * Checks whether a user has to change the password.
	 *
	 * @return bool
	 */
	public function isPasswordChangeForced(): bool
	{
		return $this->attributes['force_pass_reset'] ?? false;
	}

	//--------------------------------------------------------------------

	/**
	 * Determines whether the user has the appropriate permission,
	 * either directly, or through one of it's groups.
	 *
	 * @param string $permission
	 *
	 * @return bool
	 */
	public function can(string $permission): bool
	{
		return \in_array(\strtolower($permission), $this->getPermissions());
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the user's permissions, formatted for simple checking:.
	 *
	 * [
	 *    id => name,
	 *    id=> name,
	 * ]
	 *
	 * @return array
	 */
	public function getPermissions(): array
	{
		if (empty($this->id)) {
			throw new \RuntimeException(lang('Auth.exception.permissionWithoutUser'));
		}

		if (empty($this->permissions)) {
			$list = model(UserFullPermissionListModel::class)->getUserPermissions($this->id);

			$this->permissions = [];
			foreach ($list as $permission) {
				$this->permissions[$permission->id] = \strtolower($permission->name);
			}
		}

		return $this->permissions;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the user's roles, formatted for simple checking:.
	 *
	 * [
	 *    id => name,
	 *    id => name,
	 * ]
	 *
	 * @return array
	 */
	public function getRoles(): array
	{
		if (empty($this->id)) {
			throw new \RuntimeException(lang('Auth.exception.roleWithoutUser'));
		}

		if (empty($this->roles)) {
			$list = model(UserRoleListModel::class)->getUserRoles($this->id);

			$this->roles = [];
			foreach ($list as $role) {
				$this->roles[$role->id] = \strtolower($role->name);
			}
		}

		return $this->roles;
	}

	//--------------------------------------------------------------------

	/**
	 * Automatically converts the status name to ID.
	 *
	 * @param string $status Status name
	 */
	public function setStatus(string $status): void
	{
		if (\is_null($status)) {
			$this->attributes['status_id'] = null;
		} else {
			$list = $this->getStatusList();
			$this->attributes['status_id'] = \array_search(\strtolower($status), $list) ?? null;
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the user's status.
	 *
	 * @return null|string Status name or null
	 */
	public function getStatus(): ?string
	{
		if (empty($this->attributes['status_id'])) {
			return null;
		}

		$list = $this->getStatusList();

		return $list[$this->attributes['status_id']] ?? null;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the user's activation token hash.
	 *
	 * @return null|string Token hash or null
	 */
	public function getActivateToken(): ?string
	{
		return $this->attributes['activate_hash'] ?? null;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the user's password reset token hash.
	 *
	 * @return null|string Password reset token hash or null
	 */
	public function getPasswordResetToken(): ?string
	{
		return $this->attributes['reset_hash'] ?? null;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the full status list, formatted for simple checking.
	 *
	 * [
	 *    id => name,
	 *    id => name,
	 * ]
	 *
	 * @return array|mixed
	 */
	protected function getStatusList()
	{
		if (empty($this->status)) {
			$list = model(UserStatusModel::class)->findAll();

			$this->status = [];
			foreach ($list as $status) {
				$this->status[$status->id] = \strtolower($status->name);
			}
		}

		return $this->status;
	}
}
