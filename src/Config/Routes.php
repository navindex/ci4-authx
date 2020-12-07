<?php

use Navindex\AuthX\Config\Auth as AuthConfig;
use Navindex\AuthX\Controllers\Auth as AuthController;

/**
 * Auth routes file.
 */

//  Open the configuration file
$authConfig = new AuthConfig();

// Base URL for Auth pages
$authRoot = $authConfig->root ?? '';

// Auth controller in use
$authController = $authConfig->activeController ?? AuthController::class;

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

	// Forgot/Reset
	$routes->get('forgot', $authController . '::forgotPassword', ['as' => 'forgot']);
	$routes->post('forgot', $authController . '::attemptForgot');
	$routes->get('reset-password', $authController . '::resetPassword', ['as' => 'reset-password']);
	$routes->post('reset-password', $authController . '::attemptReset');
});

unset($authConfig, $authRoot, $authController);
