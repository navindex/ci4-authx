<?php

namespace Navindex\Auth\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Paths;
use Navindex\Auth\Config\Auth as AuthConfig;
use Psr\Log\LoggerInterface;

class Auth extends Controller
{
	/**
	 * An array of helpers to be automatically loaded
	 * upon class instantiation.
	 *
	 * @var array
	 */
	protected $helpers = ['form', 'html'];

	/**
	 * Authenticator library.
	 *
	 * @var \Navindex\Auth\Authentication\Authenticators\AuthenticatorInterface
	 */
	protected $auth;

	/**
	 * Configuration settings.
	 *
	 * @var \Navindex\Auth\Config\Auth
	 */
	protected $config;

	/**
	 * Session.
	 *
	 * @var \CodeIgniter\Session\Session
	 */
	protected $session;

	/**
	 * Callback function to prepare view data.
	 *
	 * @var callback
	 */
	protected $prepareViewData;

	/**
	 * User entity class name.
	 *
	 * @var string
	 */
	protected $userEntity;

	/**
	 * User model class name.
	 *
	 * @var string
	 */
	protected $userModel;

	//--------------------------------------------------------------------

	/**
	 * Constructor.
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		// Most services in this controller require
		// the session to be started - so fire it up!
		$this->session = service('session');

		$this->config = config(AuthConfig::class);
		$this->auth = service('authentication');

		foreach ($this->config->controllers[\get_class($this)] as $key => $value) {
			$this->{$key} = $value;
		}
	}

	//--------------------------------------------------------------------
	// Login/out
	//--------------------------------------------------------------------

	/**
	 * Displays the login form, or redirects
	 * the user to their destination/home if
	 * they are already logged in.
	 */
	public function login()
	{
		// No need to show a login form if the user
		// is already logged in.
		if ($this->auth->check()) {
			$redirectURL = session('redirect_url') ?? base_url();
			unset($_SESSION['redirect_url']);

			return redirect()->to($redirectURL);
		}

		// Set a return URL if none is specified
		$_SESSION['redirect_url'] = session('redirect_url') ?? previous_url() ?? base_url();

		return $this->view($this->config->views['login']);
	}

	//--------------------------------------------------------------------

	/**
	 * Attempts to verify the user's credentials
	 * through a POST request.
	 */
	public function attemptLogin()
	{
		$rules = [
			'login'    => 'required',
			'password' => 'required',
		];

		if ($this->config->validFields == ['email']) {
			$rules['login'] .= '|valid_email';
		}

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$login = $this->request->getPost('login');
		$password = $this->request->getPost('password');
		$remember = (bool) $this->request->getPost('remember');

		// Determine credential type
		$type = \filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

		// Try to log them in...
		if (!$this->auth->attempt([$type => $login, 'password' => $password], $remember)) {
			return redirect()->back()->withInput()->with('error', $this->auth->error() ?? lang('Auth.badAttempt'));
		}

		// Is the user being forced to reset their password?
		if (true === $this->auth->user()->force_pass_reset) {
			return redirect()->to(route_to('reset-password') . '?token=' . $this->auth->user()->reset_hash)->withCookies();
		}

		$redirectURL = session('redirect_url') ?? base_url();
		unset($_SESSION['redirect_url']);

		return redirect()->to($redirectURL)->withCookies()->with('message', lang('Auth.loginSuccess'));
	}

	//--------------------------------------------------------------------

	/**
	 * Log the user out.
	 */
	public function logout()
	{
		if ($this->auth->check()) {
			$this->auth->logout();
		}

		return redirect()->to(base_url());
	}

	//--------------------------------------------------------------------
	// Register
	//--------------------------------------------------------------------

	/**
	 * Displays the user registration page.
	 */
	public function register()
	{
		// check if already logged in.
		if ($this->auth->check()) {
			return redirect()->back();
		}

		// Check if registration is allowed
		if (!$this->config->allowRegistration) {
			return redirect()->back()->withInput()->with('error', lang('Auth.registerDisabled'));
		}

		return $this->view($this->config->views['register']);
	}

	//--------------------------------------------------------------------

	/**
	 * Attempt to register a new user.
	 */
	public function attemptRegister()
	{
		// Check if registration is allowed
		if (!$this->config->allowRegistration) {
			return redirect()->back()->withInput()->with('error', lang('Auth.registerDisabled'));
		}

		$users = model($this->userModel);

		// Validate here first, since some things,
		// like the password, can only be validated properly here.
		$rules = [
			'username'     => 'required|alpha_numeric_space|min_length[3]|is_unique[' . $users->table . '.username]',
			'email'        => 'required|valid_email|is_unique[' . $users->table . '.email]',
			'password'     => 'required|strong_password',
			'pass_confirm' => 'required|matches[password]',
		];

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', service('validation')->getErrors());
		}

		// Save the user
		$allowedPostFields = \array_merge(['password'], $this->config->validFields, $this->config->personalFields);
		$userClass = $this->userEntity;
		$user = new $userClass($this->request->getPost($allowedPostFields));

		false !== $this->config->requireActivation ? $user->generateActivateHash() : $user->activate();

		// Ensure default group gets assigned if set
		if (!empty($this->config->defaultRole)) {
			$users = $users->setDefaultRole($this->config->defaultRole);
		}

		if (!$users->save($user)) {
			return redirect()->back()->withInput()->with('errors', $users->errors());
		}

		if (false !== $this->config->requireActivation) {
			$activator = service('activator');
			$sent = $activator->send($user);

			if (!$sent) {
				return redirect()->back()->withInput()->with('error', $activator->error() ?? lang('Auth.unknownError'));
			}

			// Success!
			return redirect()->route('login')->with('message', lang('Auth.activationSuccess'));
		}

		// Success!
		return redirect()->route('login')->with('message', lang('Auth.registerSuccess'));
	}

	//--------------------------------------------------------------------
	// Forgot Password
	//--------------------------------------------------------------------

	/**
	 * Displays the forgot password form.
	 */
	public function forgotPassword()
	{
		if (false === $this->config->activeResetter) {
			return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
		}

		return $this->view($this->config->views['forgot']);
	}

	//--------------------------------------------------------------------

	/**
	 * Attempts to find a user account with that password
	 * and send password reset instructions to them.
	 */
	public function attemptForgot()
	{
		if (false === $this->config->activeResetter) {
			return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
		}

		$users = model($this->userModel);

		$user = $users->findUnique('email', $this->request->getPost('email'));

		if (\is_null($user)) {
			return redirect()->back()->with('error', lang('Auth.forgotNoUser'));
		}

		// Save the reset hash /
		$user->generateResetHash();
		$users->save($user);

		$resetter = service('resetter');
		$sent = $resetter->send($user);

		if (!$sent) {
			return redirect()->back()->withInput()->with('error', $resetter->error() ?? lang('Auth.unknownError'));
		}

		return redirect()->route('reset-password')->with('message', lang('Auth.forgotEmailSent'));
	}

	//--------------------------------------------------------------------

	/**
	 * Displays the Reset Password form.
	 */
	public function resetPassword()
	{
		if (false === $this->config->activeResetter) {
			return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
		}

		$token = $this->request->getGet('token');

		return $this->view($this->config->views['reset'], ['token'  => $token]);
	}

	//--------------------------------------------------------------------

	/**
	 * Verifies the code with the email and saves the new password,
	 * if they all pass validation.
	 *
	 * @return mixed
	 */
	public function attemptReset()
	{
		if (false === $this->config->activeResetter) {
			return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
		}

		$users = model($this->userModel);

		// First things first - log the reset attempt.
		$users->logResetAttempt(
			$this->request->getPost('email'),
			$this->request->getPost('token'),
			$this->request->getIPAddress(),
			(string) $this->request->getUserAgent()
		);

		$rules = [
			'token'        => 'required',
			'email'        => 'required|valid_email',
			'password'     => 'required|strong_password',
			'pass_confirm' => 'required|matches[password]',
		];

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $users->errors());
		}

		$user = $users->where('reset_hash', $this->request->getPost('token'))
			->findUnique('email', $this->request->getPost('email'))
		;

		if (\is_null($user)) {
			return redirect()->back()->with('error', lang('Auth.forgotNoUser'));
		}

		// Reset token still valid?
		if (!empty($user->reset_expires_at) && \time() > $user->reset_expires_at->getTimestamp()) {
			return redirect()->back()->withInput()->with('error', lang('Auth.resetTokenExpired'));
		}

		// Success! Save the new password, and cleanup the reset hash.
		$user->password = $this->request->getPost('password');
		$user->reset_hash = null;
		$user->reset_requested_at = \gmdate('Y-m-d H:i:s');
		$user->reset_expires_at = null;
		$user->force_pass_reset = false;
		$users->save($user);

		return redirect()->route('login')->with('message', lang('Auth.resetSuccess'));
	}

	//--------------------------------------------------------------------

	/**
	 * Activate account.
	 *
	 * @return mixed
	 */
	public function activateAccount()
	{
		$users = model($this->userModel);

		// First things first - log the activation attempt.
		$users->logActivationAttempt(
			$this->request->getGet('token'),
			$this->request->getIPAddress(),
			(string) $this->request->getUserAgent()
		);

		$throttler = service('throttler');

		if (false === $throttler->check($this->request->getIPAddress(), 2, MINUTE)) {
			return service('response')->setStatusCode(429)->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));
		}

		$user = $users->where('activate_hash', $this->request->getGet('token'))->inactive()->first();

		if (\is_null($user)) {
			return redirect()->route('login')->with('error', lang('Auth.activationNoUser'));
		}

		$user->activate();

		$users->save($user);

		return redirect()->route('login')->with('message', lang('Auth.registerSuccess'));
	}

	//--------------------------------------------------------------------

	/**
	 * Resend activation account.
	 *
	 * @return mixed
	 */
	public function resendActivateAccount()
	{
		if (false === $this->config->requireActivation) {
			return redirect()->route('login');
		}

		$throttler = service('throttler');

		if (false === $throttler->check($this->request->getIPAddress(), 2, MINUTE)) {
			return service('response')->setStatusCode(429)->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));
		}

		$login = \urldecode($this->request->getGet('login'));
		$type = \filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

		$users = model($this->userModel);

		$user = $users->whereNotActive()->findUnique($type, $login);

		if (\is_null($user)) {
			return redirect()->route('login')->with('error', lang('Auth.activationNoUser'));
		}

		$activator = service('activator');
		$sent = $activator->send($user);

		if (!$sent) {
			return redirect()->back()->withInput()->with('error', $activator->error() ?? lang('Auth.unknownError'));
		}

		// Success!
		return redirect()->route('login')->with('message', lang('Auth.activationSuccess'));
	}

	//--------------------------------------------------------------------

	/**
	 * Convenience method to show the view.
	 *
	 * @param string     $view View path
	 * @param null|array $data View data
	 *
	 * @throws \CodeIgniter\Exceptions\PageNotFoundException
	 *
	 * @return string The rendered view
	 */
	protected function view(string $view, array $data = null): string
	{
		$filename = config(Paths::class)->viewDirectory . DIRECTORY_SEPARATOR . $view . '.php';

		if (!\file_exists($filename)) {
			throw PageNotFoundException::forPageNotFound($view);
		}

		$data = \array_merge(['auth' => $this->config], $data ?? []);

		$prepareData = $this->prepareViewData;

		if (!empty($prepareData)) {
			$data = $prepareData($data);
		}

		return view($view, $data);
	}
}
