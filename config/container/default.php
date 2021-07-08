<?php declare(strict_types=1);

use Circli\ApiAuth\KeyFactory;
use Circli\ApiAuth\Middleware\ApiAuthenticationMiddleware;
use Circli\ApiAuth\Provider\AccessKeyProvider;
use Circli\ApiAuth\Provider\AuthProvider;
use Circli\Core\Config;
use Circli\Core\Events\PostContainerBuild;
use Circli\EventDispatcher\ListenerProvider\DefaultProvider;
use Circli\Extension\Auth\Voter\AccessCheckers;
use Circli\Extension\Auth\Web\AccessDeniedActionInterface;
use Fig\EventDispatcher\AggregateProvider;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Validator;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Circli\ApiAuth\AccessDenied\Action;
use Circli\ApiAuth\AccessVoters\AccessKeyAccessVoter;
use Circli\ApiAuth\AccessVoters\AdminRoleAccessVoter;
use Circli\ApiAuth\AccessVoters\ModuleRouteAccessVoter;
use Circli\ApiAuth\AccessVoters\RoleRouteAccessVoter;
use Circli\ApiAuth\JwtHandler;
use Circli\ApiAuth\JwtValidator;
use Circli\ApiAuth\Provider\AccountAuthProvider;
use Circli\ApiAuth\Provider\ApiAuthProvider;
use Circli\ApiAuth\Web\Input\LoginInput;
use Circli\ApiAuth\Web\Input\LoginInputInterface;
use function DI\autowire;
use function DI\decorate;
use function DI\factory;
use function DI\get;

return [
	DefaultProvider::class => decorate(static function (DefaultProvider $provider, ContainerInterface $container) {
		$provider->listen(PostContainerBuild::class, function (PostContainerBuild $event) use ($container) {
			$container->get(AggregateProvider::class)->addProvider($container->get(AccessCheckers::class));
		});
		return $provider;
	}),
	ApiAuthenticationMiddleware::class => autowire(ApiAuthenticationMiddleware::class),
	AccessDeniedActionInterface::class => autowire(Action::class),
	AccessCheckers::class => decorate(static function (AccessCheckers $accessCheckers, ContainerInterface $container) {
		$accessCheckers->addVoter(new AdminRoleAccessVoter());
		$accessCheckers->addVoter(new ModuleRouteAccessVoter($container->get(LoggerInterface::class)));
		$accessCheckers->addVoter(new RoleRouteAccessVoter($container->get(LoggerInterface::class)));
		$accessCheckers->addVoter(new AccessKeyAccessVoter());
		return $accessCheckers;
	}),
	Key::class => factory([KeyFactory::class, 'create']),
	LoginInputInterface::class => autowire(LoginInput::class),
	Signer::class => autowire(Sha256::class),
	Validator::class => autowire(JwtValidator::class),
	JwtHandler::class => autowire(JwtHandler::class),
	AuthProvider::class => autowire(ApiAuthProvider::class)->constructor(
		get(AccessKeyProvider::class),
		get(AccountAuthProvider::class),
		get(LoggerInterface::class),
	),
];
