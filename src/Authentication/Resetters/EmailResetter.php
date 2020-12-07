<?php

namespace Navindex\AuthX\Authentication\Resetters;

use Config\Email;
use Navindex\AuthX\Authentication\Resetters\ResetterInterface;
use Navindex\AuthX\Entities\UserInterface;

/**
 * Class EmailResetter.
 *
 * Sends a reset password email to user.
 */
class EmailResetter extends BaseResetter implements ResetterInterface
{
	/**
	 * Sends a reset email.
	 *
	 * @param \Navindex\AuthX\Entities\UserInterface $user User record
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function send(UserInterface $user = null): bool
	{
		if (empty($user)) {
			$this->error = lang('Auth.error.noUserToReset');

			return false;
		}

		$email = service('email');
		$emailConfig = config(Email::class);

		$settings = $this->getSettings();

		$prepareData = $settings->prepareViewData;
		$data = $prepareData();
		$data['hash'] = $user->getPasswordResetToken();

		$sent = $email->setFrom($settings->fromEmail ?? $emailConfig->fromEmail, $settings->fromName ?? $emailConfig->fromName)
			->setTo($user->email)
			->setSubject(lang('Auth.email.forgot.subject'))
			->setMessage(view($this->config->views['emailForgot'], $data))
			->setMailType('html')
			->send()
		;

		if (!$sent) {
			$this->error = lang('Auth.error.resetSendFailed', [$user->getEmail()]);

			return false;
		}

		return true;
	}
}
