<?php declare(strict_types=1);

namespace Circli\ApiAuth\Entities;

use Circli\ApiAuth\Values\Role;
use Circli\Extension\Auth\Repositories\Objects\AuthObject;

final class LocalAuthObject implements AuthObject
{
	public function __construct(
		private Role $role,
	) {}

	public function getRole(): Role
	{
		return $this->role;
	}
}
