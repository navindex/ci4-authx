<?php

use Navindex\Auth\Config\Auth;

/**
 * Auth routes file.
 */

$authConfig = new Auth();
$authRoot = $authConfig->root;
$authController = $authConfig->activeController;

// Auth
$routes->group($authRoot, ['namespace' => ''], function ($routes) use ($authController) {
	// Login/out
	$routes->get('login', $authController . '::login', ['as' => 'login']);
	$routes->post('login', $authController . '::attemptLogin');
	$routes->get('logout', $authController . '::logout');

	// Registration
	$routes->get('register', $authController . '::register', ['as' => 'register']);
	$routes->post('register', $authController . '::attemptRegister');

	// Activation
	$routes->get('activate-account', $authController . '::activateAccount', ['as' => 'activate-account']);
	$routes->get('resend-activate-account', $authController . '::resendActivateAccount', ['as' => 'resend-activate-account']);

	// Forgot/Resets
	$routes->get('forgot', $authController . '::forgotPassword', ['as' => 'forgot']);
	$routes->post('forgot', $authController . '::attemptForgot');
	$routes->get('reset-password', $authController . '::resetPassword', ['as' => 'reset-password']);
	$routes->post('reset-password', $authController . '::attemptReset');
});

unset($authConfig, $authRoot, $authController);
