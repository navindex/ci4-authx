<?php

namespace Navindex\Auth\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class LoginFilter implements FilterInterface
{
	/**
	 * Do whatever processing this filter needs to do.
	 * By default it should not return anything during
	 * normal execution. However, when an abnormal state
	 * is found, it should return an instance of
	 * CodeIgniter\HTTP\Response. If it does, script
	 * execution will end and that Response will be
	 * sent back to the client, allowing for error pages,
	 * redirects, etc.
	 *
	 * @param \CodeIgniter\HTTP\RequestInterface $request
	 * @param null|array                         $params
	 *
	 * @return mixed
	 */
	public function before(RequestInterface $request, $params = null)
	{
		if (!\function_exists('logged_in')) {
			helper('auth');
		}

		$current = (string) current_url(true)
			->setHost('')
			->setScheme('')
			->stripQuery('token')
		;

		// Make sure this isn't already a login route
		if (\in_array((string) $current, [route_to('login'), route_to('forgot'), route_to('reset-password'), route_to('register'), route_to('activate-account')])) {
			return;
		}

		// if no user is logged in then send to the login form
		$authenticate = Services::authentication();
		if (!$authenticate->check()) {
			session()->set('redirect_url', current_url());

			return redirect('login');
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Allows After filters to inspect and modify the response
	 * object as needed. This method does not allow any way
	 * to stop execution of other after filters, short of
	 * throwing an Exception or Error.
	 *
	 * @param \CodeIgniter\HTTP\RequestInterface  $request
	 * @param \CodeIgniter\HTTP\ResponseInterface $response
	 * @param null|array                          $arguments
	 */
	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
	}

	//--------------------------------------------------------------------
}
