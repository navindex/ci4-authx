<?php

namespace Navindex\AuthX\Authentication\Activators;

use Config\Email;
use Navindex\AuthX\Authentication\Activators\ActivatorInterface;
use Navindex\AuthX\Entities\UserInterface;

/**
 * Class EmailActivator.
 *
 * Sends an activation email to user.
 */
class EmailActivator extends BaseActivator implements ActivatorInterface
{
	/**
	 * Sends an activation email.
	 *
	 * @param \Navindex\AuthX\Entities\UserInterface $user User record
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function send(UserInterface $user = null): bool
	{
		if (empty($user)) {
			$this->error = lang('Auth.error.noUserToActivate');

			return false;
		}

		$email = service('email');
		$emailConfig = config(Email::class);

		$settings = $this->getSettings();

		$prepareData = $settings->prepareViewData;
		$data = $prepareData();
		$data['hash'] = $user->getActivateToken();

		$sent = $email->setFrom($settings->fromEmail ?? $emailConfig->fromEmail, $settings->fromName ?? $emailConfig->fromName)
			->setTo($user->getEmail())
			->setSubject(lang('Auth.email.activate.subject'))
			->setMessage(view($this->config->views['emailActivate'], $data))
			->setMailType('html')
			->send()
		;

		if (!$sent) {
			$this->error = lang('Auth.error.activationSendFailed', [$user->getEmail()]);

			return false;
		}

		return true;
	}
}
