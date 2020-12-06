<?php

namespace Navindex\Auth\Config;

use CodeIgniter\Config\BaseConfig;
use Navindex\Auth\Authentication\Activators\EmailActivator;
use Navindex\Auth\Authentication\Authenticators\LocalAuthenticator;
use Navindex\Auth\Authentication\Resetters\EmailResetter;
use Navindex\Auth\Authentication\Validators\CompositionValidator;
use Navindex\Auth\Authentication\Validators\DictionaryValidator;
use Navindex\Auth\Authentication\Validators\NothingPersonalValidator;
use Navindex\Auth\Authentication\Validators\PwnedValidator;
use Navindex\Auth\Authorisation\FlatAuthorisation;
use Navindex\Auth\Controllers\Auth as AuthController;
use Navindex\Auth\Entities\Type;
use Navindex\Auth\Entities\User;
use Navindex\Auth\Entities\UserToken;
use Navindex\Auth\Models\Types\PermissionModel;
use Navindex\Auth\Models\Types\RoleModel;
use Navindex\Auth\Models\UserModel;
use Navindex\Auth\Models\UserTokenModel;
use Navindex\Auth\Validation\Rules as AuthRules;

class Auth extends BaseConfig
{
	//--------------------------------------------------------------------
	// Important settings
	//--------------------------------------------------------------------

	/**
	 * Allow user registration.
	 *
	 * When enabled (default) any unregistered user may apply for a new
	 * account. If you disable registration you may need to ensure your
	 * controllers and views know not to offer registration.
	 *
	 * @var bool
	 */
	public $allowRegistration = true;

	/**
	 * Allow password reset.
	 *
	 * When enabled (default) any registered user may apply for a new
	 * password. If you disable password reset you may need to ensure your
	 * controllers and views know not to offer password reset.
	 *
	 * @var bool
	 */
	public $passwordReset = true;

	/**
	 * Require confirmation registration via email.
	 *
	 * When enabled, every registered user will receive an email message
	 * with a special link he have to confirm to activate his account.
	 *
	 * @var bool
	 */
	public $requireActivation = true;

	//--------------------------------------------------------------------

	/**
	 * Top level URI to the Auth pages.
	 *
	 * @var string
	 */
	public $root;

	//--------------------------------------------------------------------

	/**
	 * Minimum password length.
	 *
	 * The minimum length that a password must be to be accepted.
	 * Recommended minimum value by NIST = 8 characters.
	 *
	 * @var int
	 */
	public $minimumPasswordLength = 8;

	//--------------------------------------------------------------------

	/**
	 * Allow persistent login cookies (Remember me).
	 *
	 * While every attempt has been made to create a very strong protection
	 * with the remember me system, there are some cases (like when you
	 * need extreme protection, like dealing with users financials) that
	 * you might not want the extra risk associated with this cookie-based
	 * solution.
	 *
	 * @var bool
	 */
	public $allowRemembering = true;

	//--------------------------------------------------------------------

	/**
	 * Default role.
	 *
	 * The default role that will be added to a newly registered user.
	 * i.e. $defaultRole = 'guest';
	 *
	 * @var null|string
	 */
	public $defaultRole;

	//--------------------------------------------------------------------
	// Database
	//--------------------------------------------------------------------

	/**
	 * Database table prefix.
	 *
	 * @var string
	 */
	public $tablePrefix;

	//--------------------------------------------------------------------
	// Views
	//--------------------------------------------------------------------

	/**
	 * Views used by Auth controllers.
	 *
	 * @var array
	 */
	public $views = [
		'login'         => 'Navindex\Auth\Views\forms\login',
		'register'      => 'Navindex\Auth\Views\forms\register',
		'forgot'        => 'Navindex\Auth\Views\forms\forgot',
		'reset'         => 'Navindex\Auth\Views\forms\reset',
		'emailForgot'   => 'Navindex\Auth\Views\emails\forgot',
		'emailActivate' => 'Navindex\Auth\Views\emails\activate',
	];

	//--------------------------------------------------------------------

	/**
	 * Layout for the views to extend.
	 *
	 * @var string
	 */
	public $viewLayout = 'Navindex\Auth\Views\layouts\auth';

	//--------------------------------------------------------------------

	/**
	 * Layout for the emails to extend.
	 *
	 * @var string
	 */
	public $emailLayout = 'Navindex\Auth\Views\layouts\email';

	//--------------------------------------------------------------------
	// Fields
	//--------------------------------------------------------------------

	/**
	 * Fields that are available to be used as credentials for login.
	 *
	 * @var array
	 */
	public $validFields = [
		'email',
		'username',
	];

	//--------------------------------------------------------------------

	/**
	 * Additional fields for "Nothing Personal".
	 *
	 * The NothingPersonalValidator prevents personal information from
	 * being used in passwords. The email and username fields are always
	 * considered by the validator. Do not enter those field names here.
	 *
	 * An extend User Entity might include other personal info such as
	 * first and/or last names. $personalFields is where you can add
	 * fields to be considered as "personal" by the NothingPersonalValidator.
	 * For example:
	 *     $personalFields = ['firstname', 'lastname'];
	 *
	 * @var array
	 */
	public $personalFields = [];

	//--------------------------------------------------------------------
	// Password
	//--------------------------------------------------------------------

	/**
	 * Password / Username similarity.
	 *
	 *  Among other things, the NothingPersonalValidator checks the
	 *  amount of sameness between the password and username.
	 *  Passwords that are too much like the username are invalid.
	 *
	 *  The value set for $maxSimilarity represents the maximum percentage
	 *  of similarity at which the password will be accepted. In other words, any
	 *  calculated similarity equal to, or greater than $maxSimilarity
	 *  is rejected.
	 *
	 *  The accepted range is 0-100, with 0 (zero) meaning don't check similarity.
	 *  Using values at either extreme of the *working range* (1-100) is
	 *  not advised. The low end is too restrictive and the high end is too permissive.
	 *  The suggested value for $maxSimilarity is 50.
	 *
	 *  You may be thinking that a value of 100 should have the effect of accepting
	 *  everything like a value of 0 does. That's logical and probably true,
	 *  but is unproven and untested. Besides, 0 skips the work involved
	 *  making the calculation unlike when using 100.
	 *
	 *  The (admittedly limited) testing that's been done suggests a useful working range
	 *  of 50 to 60. You can set it lower than 50, but site users will probably start
	 *  to complain about the large number of proposed passwords getting rejected.
	 *  At around 60 or more it starts to see pairs like 'captain joe' and 'joe*captain ' as
	 *  perfectly acceptable which clearly they are not.
	 *
	 * @var int
	 */
	public $maxSimilarity = 50;

	//--------------------------------------------------------------------
	// Remember Me
	//--------------------------------------------------------------------

	/**
	 * Remember length.
	 *
	 * The amount of time, in seconds, that you want a login to last for.
	 * Defaults to 30 days.
	 *
	 * @var int
	 */
	public $rememberLength = 30 * DAY;

	//--------------------------------------------------------------------
	// Error handling
	//--------------------------------------------------------------------

	/**
	 * Silent run. If true, will continue instead of throwing exceptions.
	 *
	 * @var bool
	 */
	public $silent = false;

	//--------------------------------------------------------------------
	// Password hashing
	//--------------------------------------------------------------------

	/**
	 * Encryption algorithm to use.
	 *
	 * Valid values are
	 * - PASSWORD_DEFAULT (default)
	 * - PASSWORD_BCRYPT
	 * - PASSWORD_ARGON2I  - As of PHP 7.2 only if compiled with support for it
	 * - PASSWORD_ARGON2ID - As of PHP 7.3 only if compiled with support for it
	 * If you choose to use any ARGON algorithm, then you might want to
	 * uncomment the "ARGON2i/D Algorithm" options to suit your needs
	 *
	 * @var mixed
	 */
	public $hashAlgorithm = PASSWORD_DEFAULT;

	//--------------------------------------------------------------------
	// ARGON2i/D Hashing Algorithm Options
	//--------------------------------------------------------------------

	/**
	 * Maximum memory (in bytes) that may be used to compute the Argon2 hash.
	 * Default: PASSWORD_ARGON2_DEFAULT_MEMORY_COST.
	 *
	 * @var int
	 */
	public $hashMemoryCost = 2048;

	//--------------------------------------------------------------------

	/**
	 * Maximum amount of time it may take to compute the Argon2 hash.
	 * Default: PASSWORD_ARGON2_DEFAULT_TIME_COST.
	 *
	 * @var int
	 */
	public $hashTimeCost = 4;

	//--------------------------------------------------------------------

	/**
	 * Number of threads to use for computing the Argon2 hash.
	 * Default: PASSWORD_ARGON2_DEFAULT_THREADS.
	 *
	 * @var int
	 */
	public $hashThreads = 4;

	//--------------------------------------------------------------------
	// BCRYPT Hashing Algorithm Options
	//--------------------------------------------------------------------

	/**
	 * Password hashing cost.
	 *
	 * The BCRYPT method of encryption allows you to define the "cost"
	 * or number of iterations made, whenever a password hash is created.
	 * This defaults to a value of 10 which is an acceptable number.
	 * However, depending on the security needs of your application
	 * and the power of your hardware, you might want to increase the
	 * cost. This makes the hashing process takes longer.
	 * Valid range is between 4 - 31.
	 *
	 * @var int
	 */
	public $hashCost = 10;

	//--------------------------------------------------------------------
	// User Activators
	//--------------------------------------------------------------------

	/**
	 * The active activator.
	 *
	 * @var string
	 */
	public $activeActivator = EmailActivator::class;

	//--------------------------------------------------------------------

	/**
	 * Available user activators with config settings.
	 *
	 * @var array
	 */
	public $activators = [
		EmailActivator::class => [
			'fromEmail' => null,
			'fromName'  => null,
		],
	];

	//--------------------------------------------------------------------
	// Authenticators
	//--------------------------------------------------------------------

	/**
	 * The list of active authenticators.
	 *
	 * @var string
	 */
	public $activeAuthenticators = [
		'local' => LocalAuthenticator::class,
	];

	//--------------------------------------------------------------------

	/**
	 * Available authenticators with config settings.
	 *
	 * @var array
	 */
	public $authenticators = [
		LocalAuthenticator::class => [
			'userModel'  => UserModel::class,
			'tokenModel' => UserTokenModel::class,
		],
	];

	//--------------------------------------------------------------------
	// Resetters
	//--------------------------------------------------------------------

	/**
	 * The active resetter. When enabled, every user will have the option
	 * to reset his passwordv ia specified resetter.
	 *
	 * @var null|string
	 */
	public $activeResetter = EmailResetter::class;

	//--------------------------------------------------------------------

	/**
	 * Available resetters with config settings.
	 *
	 * @var array
	 */
	public $resetters = [
		EmailResetter::class => [
			'fromEmail' => null,
			'fromName'  => null,
		],
	];

	//--------------------------------------------------------------------

	/**
	 * Reset time. The amount of time that a password reset-token is
	 * valid for, in seconds.
	 *
	 * @var int
	 */
	public $resetTime = 3600;

	//--------------------------------------------------------------------
	// Validators
	//--------------------------------------------------------------------

	/**
	 * The list of active validators.
	 *
	 * The Validator class runs the password through all of these
	 * classes, each getting the opportunity to pass/fail the password.
	 * You can add custom classes as long as they adhere to the
	 * Validator\ValidatorInterface.
	 *
	 * @var array
	 */
	public $activeValidators = [
		CompositionValidator::class,
		NothingPersonalValidator::class,
		DictionaryValidator::class,
	];

	//--------------------------------------------------------------------

	/**
	 * Available validators with config settings.
	 *
	 * @var array
	 */
	public $validators = [
		CompositionValidator::class     => [],
		NothingPersonalValidator::class => [],
		DictionaryValidator::class      => [],
		PwnedValidator::class           => [],
	];

	//--------------------------------------------------------------------
	// Authorisators
	//--------------------------------------------------------------------

	/**
	 * The active authorisator.
	 *
	 * @var string
	 */
	public $activeAuthorisator = FlatAuthorisation::class;

	//--------------------------------------------------------------------

	/**
	 * Available authorisators with config settings.
	 *
	 * @var array
	 */
	public $authorisators = [
		FlatAuthorisation::class => [
			'models' => [
				'user'       => UserModel::class,
				'token'      => UserTokenModel::class,
				'role'       => RoleModel::class,
				'permission' => PermissionModel::class,
			],
			'entities' => [
				'user'       => User::class,
				'token'      => UserToken::class,
				'role'       => Type::class,
				'permission' => Type::class,
			],
		],
	];

	//--------------------------------------------------------------------
	// Controllers
	//--------------------------------------------------------------------

	/**
	 * The active controller.
	 *
	 * @var string
	 */
	public $activeController = AuthController::class;

	//--------------------------------------------------------------------

	/**
	 * Available controllers with config settings.
	 *
	 * @var array
	 */
	public $controllers = [
		AuthController::class => [
			'userEntity' => User::class,
			'userModel'  => UserModel::class,
		],
	];

	//--------------------------------------------------------------------
	// Validation Rules
	//--------------------------------------------------------------------

	/**
	 * Available validation rules with config settings.
	 *
	 * @var array
	 */
	public $rules = [
		AuthRules::class => [
			'user' => User::class,
		],
	];
}
