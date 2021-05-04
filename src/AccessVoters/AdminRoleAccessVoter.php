<?php declare(strict_types=1);

namespace Circli\ApiAuth\AccessVoters;

use Circli\ApiAuth\Entities\ApiAuthObject;
use Circli\ApiAuth\Entities\TokenAccount;
use Circli\ApiAuth\Values\Role;
use Circli\Extension\Auth\Repositories\Objects\AuthObject;
use Circli\Extension\Auth\Voter\AccessRequestEventInterface;
use Circli\Extension\Auth\Voter\VoterInterface;

final class AdminRoleAccessVoter implements VoterInterface
{
	private ?TokenAccount $object = null;

	public function setAuthObject(AuthObject $object): void
	{
		if ($object instanceof ApiAuthObject && $object->getUserObject() instanceof TokenAccount) {
			$this->object = $object->getUserObject();
		}
	}

	public function supports(AccessRequestEventInterface $accessRequest): bool
	{
		return $this->object !== null;
	}

	public function __invoke(AccessRequestEventInterface $accessRequestEvent): void
	{
		if ($this->object && $this->object->getRole()->is(Role::ADMIN())) {
			$accessRequestEvent->allow();
		}
	}
}
