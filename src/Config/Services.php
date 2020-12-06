<?php

namespace Navindex\Auth\Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Config\Services as CoreServices;
use Navindex\Auth\Authentication\Activators\Activator;
use Navindex\Auth\Authentication\Resetters\Resetter;
use Navindex\Auth\Authentication\Validators\Validator;
use Navindex\Auth\Config\Auth;
use Navindex\Auth\Models\Types\PermissionModel;
use Navindex\Auth\Models\Types\RoleModel;
use Navindex\Auth\Models\UserModel;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends CoreServices
{
	/**
	 * User authentication.
	 *
	 * @param null|string $lib       Authenticator libraray (see Auth config)
	 * @param null|bool   $getShared Shall we use a shared instance?
	 *
	 * @return object Authentication
	 */
	public static function authentication(string $lib = 'local', bool $getShared = true)
	{
		if ($getShared) {
			return self::getSharedInstance('authentication', $lib);
		}

		$config = config(Auth::class);

		$libClass = $config->activeAuthenticators[$lib];

		return new $libClass($config);
	}

	//--------------------------------------------------------------------

	/**
	 * User authorisation.
	 *
	 * @param null|string $lib       Authorisation libraray (see Auth config)
	 * @param null|bool   $getShared Shall we use a shared instance?
	 *
	 * @return object Authorisation
	 */
	public static function authorisation(string $lib = 'flat', bool $getShared = true)
	{
		if ($getShared) {
			return self::getSharedInstance('authorisation');
		}

		$config = config(Auth::class);

		$userClass = $config->models['user'] ?? UserModel::class;
		$userModel = new $userClass();

		$roleClass = $config->models['role'] ?? RoleModel::class;
		$roleModel = new $roleClass();

		$permissionClass = $config->models['permission'] ?? PermissionModel::class;
		$permissionModel = new $permissionClass($config);

		$libClass = $config->authorisationLibs[$lib];
		$instance = new $libClass($roleModel, $permissionModel);

		return $instance->setUserModel($userModel);
	}

	//--------------------------------------------------------------------

	/**
	 * Returns an instance of the password validator.
	 *
	 * @param null|\CodeIgniter\Config\BaseConfig $config    Configuration settings
	 * @param null|bool                           $getShared Shall we use a shared instance?
	 *
	 * @return \Navindex\Auth\Authentication\Validators\PasswordValidator
	 */
	public static function passwords(BaseConfig $config = null, bool $getShared = true)
	{
		if ($getShared) {
			return self::getSharedInstance('passwords', $config);
		}

		return new Validator($config ?? config(Auth::class));
	}

	//--------------------------------------------------------------------

	/**
	 * Returns an instance of the activator.
	 *
	 * @param null|\CodeIgniter\Config\BaseConfig $config    Configuration settings
	 * @param null|bool                           $getShared Shall we use a shared instance?
	 *
	 * @return \Navindex\Auth\Authentication\Activators\UserActivator
	 */
	public static function activator(BaseConfig $config = null, bool $getShared = true)
	{
		if ($getShared) {
			return self::getSharedInstance('activator', $config);
		}

		return new Activator($config ?? config(Auth::class));
	}

	//--------------------------------------------------------------------

	/**
	 * Returns an instance of the resetter.
	 *
	 * @param null|\CodeIgniter\Config\BaseConfig $config    Configuration settings
	 * @param null|bool                           $getShared Shall we use a shared instance?
	 *
	 * @return \Navindex\Auth\Authentication\Resetters\UserResetter
	 */
	public static function resetter(BaseConfig $config = null, bool $getShared = true)
	{
		if ($getShared) {
			return self::getSharedInstance('resetter', $config);
		}

		return new Resetter($config ?? config(Auth::class));
	}
}
