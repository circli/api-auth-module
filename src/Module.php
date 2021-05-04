<?php declare(strict_types=1);

namespace Circli\ApiAuth;

use Circli\ApiAuth\Values\Role;
use Circli\ApiAuth\Values\RoleInterface;
use Circli\ApiAuth\Web\Actions\AuthenticateAction;
use Circli\Contracts\InitHttpApplication;
use Circli\Contracts\ModuleInterface;
use Circli\Contracts\PathContainer;
use Polus\Router\RouterCollection;
use Psr\Container\ContainerInterface;

final class Module implements ModuleInterface, InitHttpApplication
{
	/**
	 * @return RoleInterface[]
	 */
	public static function getRoles(): array
	{
		return [Role::ACCESS_KEY()];
	}

	/**
	 * @return string[]|array<string, mixed>
	 */
	public function configure(PathContainer $paths = null): array
	{
		$configFolder = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'container';
		return [
			$configFolder . DIRECTORY_SEPARATOR . 'default.php',
		];
	}

	public function initHttp(RouterCollection $router, ContainerInterface $container = null): void
	{
		$router->post('/auth/login', new AuthenticateAction());
	}
}
