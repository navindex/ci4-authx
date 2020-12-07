<?php

return [
	// Exceptions

	'exception' => [
		'permissionWithoutUser' => 'Users must be created before getting permissions.',
		'roleWithoutUser'       => 'Users must be created before getting roles.',
		'invalidModel'          => 'The {0} model must be loaded prior to use.',
		'userNotFound'          => 'Unable to locate a user with ID = {0, number}.',
		'noUserEntity'          => 'User details must be provided for password validation.',
		'tooManyCredentials'    => 'You may only validate against 1 credential other than a password.',
		'invalidFields'         => 'The "{0}" field cannot be used to validate credentials.',
		'unsetPasswordLength'   => 'You must set the `minimumPasswordLength` setting in the Auth config file.',
		'unknownError'          => 'Sorry, we encountered an issue sending the email to you. Please try again later.',
		'notLoggedIn'           => 'You must be logged in to access that page.',
		'notEnoughPrivilege'    => 'You do not have sufficient permissions to access that page.',
	],
	'error' => [
		'noUserToActivate'       => 'User details must be provided for activation.',
		'activationSendFailed'   => 'Unable to send email with activation message to: {0}',
		'userIsBanned'           => 'User has been banned. Contact the administrator',
		'notActivated'           => 'This user account is not yet activated.',
		'activationResend'       => 'Resend activation message one more time.',
		'noUserToReset'          => 'User details must be provided for password reset.',
		'resetSendFailed'        => 'Unable to send email with password reset instructions to: {0}',
	],
	// Registration
	'registerDisabled'          => 'Sorry, new user accounts are not allowed at this time.',
	'registerSuccess'           => 'Welcome aboard! Please login with your new credentials.',
	'registerCLI'               => 'New user created: {0}, #{1}',

	// Activation
	'activationNoUser'          => 'Unable to locate a user with that activation code.',
	'activationSuccess'         => 'Please confirm your account by clicking the activation link in the email we have sent.',

	// Login
	'badAttempt'                => "Unable to log you in.\nPlease check your credentials.",
	'loginSuccess'              => 'Welcome back!',
	'invalidPassword'           => "Unable to log you in.\nPlease check your password.",

	// Forgotten Passwords
	'forgotDisabled'            => 'Resseting password option has been disabled.',
	'forgotNoUser'              => 'Unable to locate a user with that email.',
	'resetSuccess'              => 'Your password has been successfully changed. Please login with the new password.',
	'forgotEmailSent'           => 'A security token has been emailed to you. Enter it in the box below to continue.',

	// Passwords
	'errorPasswordLength'       => 'Passwords must be at least {0, number} characters long.',
	'suggestPasswordLength'     => 'Pass phrases - up to 255 characters long - make more secure passwords that are easy to remember.',
	'errorPasswordCommon'       => 'Password must not be a common password.',
	'suggestPasswordCommon'     => 'The password was checked against over 65k commonly used passwords or passwords that have been leaked through hacks.',
	'errorPasswordPersonal'     => 'Passwords cannot contain re-hashed personal information.',
	'suggestPasswordPersonal'   => 'Variations on your email address or username should not be used for passwords.',
	'errorPasswordTooSimilar'   => 'Password is too similar to the username.',
	'suggestPasswordTooSimilar' => 'Do not use parts of your username in your password.',
	'errorPasswordPwned'        => 'The password {0} has been exposed due to a data breach and has been seen {1, number} times in {2} of compromised passwords.',
	'suggestPasswordPwned'      => '{0} should never be used as a password. If you are using it anywhere change it immediately.',
	'errorPasswordEmpty'        => 'A Password is required.',
	'passwordChangeSuccess'     => 'Password changed successfully',
	'userDoesNotExist'          => 'Password was not changed. User does not exist',
	'resetTokenExpired'         => 'Sorry. Your reset token has expired.',
	'database'                  => 'a database',
	'databases'                 => 'databases',

	// Roles
	'groupNotFound'             => 'Unable to locate group: {0}.',

	// Permissions
	'permissionNotFound'        => 'Unable to locate permission: {0}',

	// Too many requests
	'tooManyRequests'           => 'Too many requests. Please wait {0, number} seconds.',

	// Login views
	'home'                      => 'Home',
	'current'                   => 'Current',

	// Forms
	'form' => [
		'login' => [
			'title'  => 'Login',
			'action' => 'Login',
		],
		'register' => [
			'title'  => 'Register',
			'action' => 'Register',
		],
		'forgot' => [
			'title'  => 'Forgot Your Password?',
			'action' => 'Send Instructions',
		],
		'reset' => [
			'title'  => 'Reset Your Password',
			'action' => 'Reset Password',
		],
		'alreadyRegistered'         => 'Already registered?',
		'signIn'                    => 'Sign In',
		'needAnAccount'             => 'Need an account?',
		'forgotYourPassword'        => 'Forgot your password?',
		'email'                     => 'Email',
		'username'                  => 'Username',
		'emailOrUsername'           => 'Email or username',
		'password'                  => 'Password',
		'repeatPassword'            => 'Repeat Password',
		'rememberMe'                => 'Remember me',
		'token'                     => 'Token',
		'newPassword'               => 'New Password',
		'repeatNewPassword'         => 'Repeat New Password',
		'weNeverShare'              => 'We will never share your email with anyone else.',
		'enterEmailForInstructions' => 'No problem! Enter your email below and we will send instructions to reset your password.',
		'enterCodeEmailPassword'    => 'Enter the code you received via email, your email address, and your new password.',
	],

	// Emails
	'email' => [
		'greeting'  => 'Hi there,',
		'signature' => 'Regards,<br><em>{0}</em>',
		'activate'  => [
			'subject'    => 'Activate your account',
			'whatIsThis' => 'This is activation email for your account on {0}.',
			'whatToDo'   => 'To activate your account use the following link:',
			'target'     => 'Activate Account',
			'ignore'     => 'If you did not registered on this website, you can safely ignore this email.',
		],
		'forgot' => [
			'subject'    => 'Password Reset Instructions',
			'whatIsThis' => 'Someone requested a password reset at this email address for {0}.',
			'whatToDo'   => 'To reset the password use this code or URL and follow the instructions.',
			'code'       => 'Your Token: {0}',
			'target'     => 'Reset Form',
			'ignore'     => 'If you did not request a password reset, you can safely ignore this email.',
		],
	],
];
