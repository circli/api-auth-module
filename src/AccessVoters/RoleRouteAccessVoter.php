<?php declare(strict_types=1);

namespace Circli\ApiAuth\AccessVoters;

use Circli\ApiAuth\Entities\ApiAuthObject;
use Circli\ApiAuth\Entities\TokenAccount;
use Circli\ApiAuth\Values\Role;
use Circli\ApiAuth\Values\RoleInterface;
use Circli\Extension\Auth\Events\RouteAccessRequest;
use Circli\Extension\Auth\Repositories\Objects\AuthObject;
use Circli\Extension\Auth\Voter\AccessRequestEventInterface;
use Circli\Extension\Auth\Voter\VoterInterface;
use Psr\Log\LoggerInterface;
use Sunkan\Enum\EnumSet;

final class RoleRouteAccessVoter implements VoterInterface
{
	private ?TokenAccount $object = null;

	public function __construct(
		private LoggerInterface $logger,
	) {}

	public function setAuthObject(AuthObject $object): void
	{
		if ($object instanceof ApiAuthObject && $object->getUserObject() instanceof TokenAccount) {
			$this->object = $object->getUserObject();
		}
	}

	public function supports(AccessRequestEventInterface $accessRequest): bool
	{
		return $accessRequest instanceof RouteAccessRequest;
	}

	public function __invoke(AccessRequestEventInterface $accessRequestEvent): void
	{
		if (!$accessRequestEvent instanceof RouteAccessRequest) {
			return;
		}
		$handler = $accessRequestEvent->getRoute()->getHandler();
		$checkRoles = new EnumSet(RoleInterface::class);
		$deny = true;
		if (is_object($handler) && method_exists($handler, 'getRoles')) {
			$handlerRoles = $handler->getRoles();

			if (is_string($handlerRoles)) {
				$handlerRoles = [$handlerRoles];
			}
			/** @var RoleInterface|string $role */
			foreach ($handlerRoles as $role) {
				if (!$role instanceof RoleInterface) {
					$role = Role::fromValue($role);
				}
				// Skip check of access key. Handle by separate voter
				if ($role->is(Role::ACCESS_KEY())) {
					$deny = false;
					continue;
				}
				$checkRoles->attach($role);
			}
		}

		if (count($checkRoles)) {
			$checkRoles->attach(Role::ADMIN());
			$object = $this->getTokenAccount($accessRequestEvent);
			if ($object && $checkRoles->have($object->getRole())) {
				$accessRequestEvent->allow();
			}
			else {
				if ($deny) {
					$this->logger->debug('AccessVoting route denied', [
						'role' => $object ? $object->getRole() : Role::ALL(),
						'object' => $object,
					]);
					$accessRequestEvent->deny();
				}
			}
		}
	}

	private function getTokenAccount(RouteAccessRequest $accessRequestEvent): ?TokenAccount
	{
		$object = $this->object ?? $accessRequestEvent->getAuth()->getObject();
		if ($object instanceof TokenAccount) {
			return $object;
		}

		if ($object instanceof ApiAuthObject) {
			$account = $object->getUserObject();
			return $account instanceof TokenAccount ? $account : null;
		}

		return null;
	}
}
