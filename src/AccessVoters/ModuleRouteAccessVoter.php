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

final class ModuleRouteAccessVoter implements VoterInterface
{
	private ?TokenAccount $object;

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
		/** @var object $handler */
		$handler = $accessRequestEvent->getRoute()->getHandler();
		$parts = explode('\\', get_class($handler));
		$moduleNs = implode('\\', array_slice($parts, 0, count($parts) - 3));
		$moduleClass = $moduleNs . '\Module';
		if (!class_exists($moduleClass)) {
			$moduleNs = implode('\\', array_slice($parts, 0, count($parts) - 2));
			$moduleClass = $moduleNs . '\Module';
		}
		if (!class_exists($moduleClass)) {
			return;
		}
		$checkRoles = new EnumSet(RoleInterface::class);
		if (method_exists($moduleClass, 'getRoles')) {
			$moduleRoles = $moduleClass::getRoles();
			if (is_string($moduleRoles)) {
				$moduleRoles = [$moduleRoles];
			}
			/** @var RoleInterface|string $role */
			foreach ($moduleRoles as $role) {
				if (!$role instanceof RoleInterface) {
					$role = Role::fromValue($role);
				}
				$checkRoles->attach($role);
			}
		}

		if (count($checkRoles)) {
			$object = $this->getTokenAccount($accessRequestEvent);

			$checkRoles->attach(Role::ADMIN());
			if ($object && $checkRoles->have($object->getRole())) {
				$accessRequestEvent->allow();
			}
			elseif ($checkRoles->have(Role::ALL())) {
				$accessRequestEvent->allow();
			}
			else {
				$this->logger->debug('AccessVoting module failed', [
					'role' => $object ? $object->getRole() : Role::ALL(),
				]);
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
