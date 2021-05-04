<?php declare(strict_types=1);

namespace Circli\ApiAuth\Entities;

use Circli\Extension\Auth\Repositories\Objects\AuthObject;

final class ApiAuthObject implements AuthObject
{
	public function __construct(
		private AuthObject $apiObject,
		private AuthObject $userObject,
	) {}

	public function getApiObject(): AuthObject
	{
		return $this->apiObject;
	}

	public function getUserObject(): AuthObject
	{
		return $this->userObject;
	}
}
