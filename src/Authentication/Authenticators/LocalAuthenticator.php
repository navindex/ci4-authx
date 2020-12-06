<?php

namespace Navindex\Auth\Authentication\Authenticators;

use CodeIgniter\Events\Events;
use CodeIgniter\Router\Exceptions\RedirectException;
use Config\App as AppConfig;
use Navindex\Auth\Authentication\Authenticators\AuthenticatorInterface;
use Navindex\Auth\Authentication\Authenticators\BaseAuthentication;
use Navindex\Auth\Config\Auth;
use Navindex\Auth\Entities\UserInterface;
use Navindex\Auth\Exceptions\AuthException;
use Navindex\Auth\Exceptions\UserNotFoundException;

class LocalAuthenticator extends BaseAuthentication implements AuthenticatorInterface
{
	/**
	 * User model.
	 *
	 * @var \Navindex\Auth\Models\UserModelInterface
	 */
	protected $userModel;

	/**
	 * Token model.
	 *
	 * @var \Navindex\Auth\Models\TokenModelInterface
	 */
	protected $tokenModel;

	/**
	 * User entity object type name.
	 *
	 * @var string
	 */
	protected $userType;

	//--------------------------------------------------------------------

	/**
	 * Constructor.
	 *
	 * @param \Navindex\Auth\Config\Auth $config Configuration settings
	 */
	public function __construct(Auth $config)
	{
		parent::__construct($config);

		$settings = $this->config->authenticators[\get_class($this)];
		$this->userModel = model($settings['userModel']);
		$this->tokenModel = model($settings['tokenModel']);
		$this->userType = $settings['userEntity'] ?? UserInterface::class;
	}

	//--------------------------------------------------------------------

	/**
	 * Attempts to validate the credentials and log a user in.
	 *
	 * @param array $credentials User credentials
	 * @param bool  $remember    Should we remember the user (if enabled)
	 *
	 * @return bool True for successful validation and login, false otherwise
	 */
	public function attempt(array $credentials, bool $remember = null): bool
	{
		$this->user = $this->validate($credentials, true);

		if (empty($this->user)) {
			// Always record a login attempt, whether success or not.
			$request = service('request');
			$this->userModel->logLoginAttempt(
				$credentials['email'] ?? $credentials['username'],
				false,
				null,
				$request->getIPAddress(),
				$request->getUserAgent()
			);
			$this->user = null;

			return false;
		}

		if ($this->user->isBanned()) {
			// Always record a login attempt, whether success or not.
			$request = service('request');
			$this->userModel->logLoginAttempt(
				$credentials['email'] ?? $credentials['username'],
				false,
				$this->id(),
				$request->getIPAddress(),
				$request->getUserAgent()
			);
			$this->error = lang('Auth.error.userIsBanned');
			$this->user = null;

			return false;
		}

		if (!$this->user->isActive()) {
			// Always record a login attempt, whether success or not.
			$request = service('request');
			$this->userModel->logLoginAttempt(
				$credentials['email'] ?? $credentials['username'],
				false,
				$this->id(),
				$request->getIPAddress(),
				$request->getUserAgent()
			);

			$param = \http_build_query([
				'login' => \urlencode($credentials['email'] ?? $credentials['username']),
			]);

			$this->error = lang('Auth.error.notActivated') . ' ' . anchor(route_to('resend-activate-account') . '?' . $param, lang('Auth.error.activationResend'));
			$this->user = null;

			return false;
		}

		return $this->login($this->user, $remember);
	}

	//--------------------------------------------------------------------

	/**
	 * Checks to see if the user is logged in or not. Redirects to password
	 * reset if it was forced.
	 *
	 * @throws \CodeIgniter\Router\Exceptions\RedirectException
	 *
	 * @return bool True if the user is logged in
	 */
	public function check(): bool
	{
		if ($this->isLoggedIn()) {
			// Do we need to force the user to reset their password?
			if ($this->user && $this->user->isPasswordChangeForced()) {
				throw new RedirectException(route_to('reset-password') . '?token=' . $this->user->getPasswordResetToken());
			}

			return true;
		}

		// Check the remember me functionality.
		helper('cookie');
		$remember = get_cookie('remember');

		if (empty($remember)) {
			return false;
		}

		[$selector, $validator] = \explode(':', $remember);
		$validator = \hash('sha256', $validator);

		$token = $this->loginModel->getRememberToken($selector);

		if (empty($token)) {
			return false;
		}

		if (!\hash_equals($token->hashedValidator, $validator)) {
			return false;
		}

		// Yay! We were remembered!
		$user = $this->userModel->fetch($token->user_id);

		if (empty($user)) {
			return false;
		}

		$this->login($user);

		// We only want our remember me tokens to be valid
		// for a single use.
		$this->refreshRemember($user->getUserId(), $selector);

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Checks the user's credentials to see if they could authenticate.
	 * Unlike `attempt()`, will not log the user into the system.
	 *
	 * @param array $credentials User credentials
	 * @param bool  $returnUser  Return the user record?
	 *
	 * @return bool|\Navindex\Auth\Entities\UserInterface Validation result or user entity
	 */
	public function validate(array $credentials, bool $returnUser = false)
	{
		// Can't validate without a password.
		if (empty($credentials['password']) || \count($credentials) < 2) {
			return false;
		}

		// Only allowed 1 additional credential other than password
		$password = $credentials['password'];
		unset($credentials['password']);

		if (\count($credentials) > 1) {
			throw AuthException::forTooManyCredentials();
		}

		// Ensure that the fields are allowed validation fields
		if (!\in_array(\key($credentials), $this->config->validFields)) {
			throw AuthException::forInvalidFields(\key($credentials));
		}

		// Can we find a user with those credentials?
		$user = $this->userModel->fetchByCredentials($credentials);

		if (empty($user) || !\is_a($user, $this->userType)) {
			$this->error = lang('Auth.badAttempt');

			return false;
		}

		// Now, try matching the passwords.
		$result = \password_verify(\base64_encode(\hash('sha384', $password, true)), $user->getPassword());

		if (!$result) {
			$this->error = lang('Auth.invalidPassword');

			return false;
		}

		// Check to see if the password needs to be rehashed.
		// This would be due to the hash algorithm or hash
		// cost changing since the last time that a user
		// logged in.
		if (\password_needs_rehash($user->getPassword(), $this->config->hashAlgorithm)) {
			$user->setPassword($password);
			$this->userModel->save($user->getUserId(), $user);
		}

		return $returnUser
			? $user
			: true;
	}

	//--------------------------------------------------------------------

	/**
	 * Logs a user into the system.
	 * NOTE: does not perform validation. All validation should
	 * be done prior to using the login method.
	 *
	 * @param \Navindex\Auth\Entities\UserInterface $user     User record
	 * @param bool                                  $remember Should we remember the user (if enabled)
	 *
	 * @throws \Exception
	 *
	 * @return bool True if the login was successful, false otherwise
	 */
	public function login(UserInterface $user = null, bool $remember = false): bool
	{
		if (empty($user)) {
			$this->user = null;

			return false;
		}

		$this->user = $user;

		// Always record a login attempt
		$request = service('request');
		$this->userModel->logLoginAttempt(
			$user->getEmail(),
			true,
			$user->getUserId(),
			$request->getIPAddress(),
			$request->getUserAgent()
		);

		// Regenerate the session ID to help protect against session fixation
		if (ENVIRONMENT !== 'testing') {
			session()->regenerate();
		}

		// Let the session know we're logged in
		session()->set('logged_in', $this->id());

		// When logged in, ensure cache control headers are in place
		service('response')->noCache();

		if ($remember && $this->config->allowRemembering) {
			$this->rememberUser($this->id());
		}

		// We'll give a 20% chance to need to do a purge since we
		// don't need to purge THAT often, it's just a maintenance issue.
		// to keep the table from getting out of control.
		if (\mt_rand(1, 100) < 20) {
			$this->tokenModel->purgeExpiredTokens();
		}

		// trigger login event, in case anyone cares
		Events::trigger('login', $user);

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Checks to see if the user is logged in.
	 *
	 * @return bool
	 */
	public function isLoggedIn(): bool
	{
		// On the off chance
		if (\is_a($this->user, $this->userType)) {
			return true;
		}

		if ($userID = session('logged_in')) {
			// Store our current user object
			$this->user = $this->userModel->fetch($userID);

			return \is_a($this->user, $this->userType);
		}

		return false;
	}

	//--------------------------------------------------------------------

	/**
	 * Logs a user into the system by their ID.
	 *
	 * @param int  $userID   User ID
	 * @param bool $remember Should we remember the user (if enabled)
	 */
	public function loginByID(int $userID, bool $remember = false)
	{
		$user = $this->userModel->fetch($userID);

		if (empty($user)) {
			throw UserNotFoundException::forUserID($userID);
		}

		return $this->login($user, $remember);
	}

	//--------------------------------------------------------------------

	/**
	 * Logs a user out of the system.
	 */
	public function logout(): void
	{
		helper('cookie');

		// Destroy the session data - but ensure a session is still
		// available for flash messages, etc.
		if (isset($_SESSION)) {
			foreach ($_SESSION as $key => $value) {
				$_SESSION[$key] = null;
				unset($_SESSION[$key]);
			}
		}

		// Regenerate the session ID for a touch of added safety.
		session()->regenerate(true);

		// Take care of any remember me functionality
		$this->tokenModel->purgeUserTokens($this->id());

		// trigger logout event
		Events::trigger('logout', $this->user);
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the User ID for the current logged in user.
	 *
	 * @return null|int User ID or null
	 */
	public function id(): ?int
	{
		return $this->user ? $this->user->getUserId() : null;
	}

	//--------------------------------------------------------------------

	/**
	 * Generates a timing-attack safe remember me token
	 * and stores the necessary info in the db and a cookie.
	 *
	 * @see https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
	 *
	 * @param int $userID User ID
	 *
	 * @throws \Exception
	 */
	public function rememberUser(int $userID): void
	{
		$selector = \bin2hex(\random_bytes(12));
		$validator = \bin2hex(\random_bytes(20));
		$expires = \date('Y-m-d H:i:s', \time() + $this->config->rememberLength);

		$token = $selector . ':' . $validator;

		// Store it in the database
		$this->tokenModel->rememberUser($userID, $selector, \hash('sha256', $validator), $expires);

		// Save it to the user's browser in a cookie.
		$appConfig = config(AppConfig::class);
		$response = service('response');

		// Create the cookie
		$response->setCookie(
			'remember',                     // Cookie Name
			$token,                         // Value
			$this->config->rememberLength,  // # Seconds until it expires
			$appConfig->cookieDomain,
			$appConfig->cookiePath,
			$appConfig->cookiePrefix,
			false,                          // Only send over HTTPS?
			true                            // Hide from Javascript?
		);
	}

	//--------------------------------------------------------------------

	/**
	 * Sets a new validator for this user/selector. This allows
	 * a one-time use of remember-me tokens, but still allows
	 * a user to be remembered on multiple browsers/devices.
	 *
	 * @param int    $userID   User ID
	 * @param string $selector Selector device
	 */
	public function refreshRemember(int $userID, string $selector): void
	{
		$existing = $this->tokenModel->getToken($selector);

		// No matching record? Shouldn't happen, but remember the user now.
		if (empty($existing)) {
			$this->rememberUser($userID);

			return;
		}

		// Update the validator in the database and the session
		$validator = \bin2hex(\random_bytes(20));

		$this->tokenModel->updateValidator($selector, $validator);

		// Save it to the user's browser in a cookie.
		helper('cookie');

		$appConfig = config(AppConfig::class);

		// Create the cookie
		set_cookie(
			'remember',               // Cookie Name
			$selector . ':' . $validator, // Value
			$this->config->rememberLength,  // # Seconds until it expires
			$appConfig->cookieDomain,
			$appConfig->cookiePath,
			$appConfig->cookiePrefix,
			false,                  // Only send over HTTPS?
			true                  // Hide from Javascript?
		);
	}
}
