<?php declare(strict_types=1);

namespace Circli\ApiAuth\AccessVoters;

use Circli\ApiAuth\Entities\ApiAuthObject;
use Circli\ApiAuth\Values\Role;
use Circli\ApiAuth\Values\RoleInterface;
use Circli\Extension\Auth\Events\RouteAccessRequest;
use Circli\Extension\Auth\Repositories\Objects\AuthObject;
use Circli\Extension\Auth\Repositories\Objects\NullAuthObject;
use Circli\Extension\Auth\Voter\AccessRequestEventInterface;
use Circli\Extension\Auth\Voter\VoterInterface;
use Sunkan\Enum\EnumSet;

final class AccessKeyAccessVoter implements VoterInterface
{
	public function setAuthObject(AuthObject $object): void
	{
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
		if ($this->routeNeedAccessKey($accessRequestEvent) || $this->moduleNeedAccessKey($accessRequestEvent)) {
			$authObject = $accessRequestEvent->getAuth()->getObject();
			if ($authObject instanceof ApiAuthObject) {
				if (!$authObject->getApiObject() instanceof NullAuthObject) {
					$accessRequestEvent->allow();
				}
			}
		}
	}

	private function routeNeedAccessKey(RouteAccessRequest $accessRequestEvent): bool
	{
		$handler = $accessRequestEvent->getRoute()->getHandler();
		$checkRoles = new EnumSet(RoleInterface::class);
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
				$checkRoles->attach($role);
			}
		}
		return $checkRoles->have(Role::ACCESS_KEY());
	}

	private function moduleNeedAccessKey(RouteAccessRequest $accessRequestEvent): bool
	{
		/** @var object $handler */
		$handler = $accessRequestEvent->getRoute()->getHandler();
		$parts = explode('\\', get_class($handler));
		$moduleNs = implode('\\', array_slice($parts, 0, count($parts) - 3));
		/** @var class-string $moduleClass */
		$moduleClass = $moduleNs . '\Module';
		$checkRoles = new EnumSet(RoleInterface::class);
		if (class_exists($moduleClass)) {
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
		}
		return $checkRoles->have(Role::ACCESS_KEY());
	}
}
