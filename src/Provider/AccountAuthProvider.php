<?php declare(strict_types=1);

namespace Circli\ApiAuth\Provider;

use Circli\ApiAuth\Entities\TokenAccount;
use Circli\ApiAuth\Events\AccountAuthenticated;
use Circli\ApiAuth\Events\AuthenticationFailed;
use Circli\ApiAuth\JwtHandler;
use Circli\ApiAuth\Repositories\AuthRepositoryInterface;
use Circli\ApiAuth\RequestAttributeKeys;
use Circli\Extension\Auth\Repositories\Objects\AuthObject;
use Circli\Extension\Auth\Repositories\Objects\NullAuthObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class AccountAuthProvider implements AuthProvider
{
	public const ACCOUNT_KEY = 'auth:account';

	private TokenAccount $account;

	public function __construct(
		private AuthRepositoryInterface $authRepository,
		private JwtHandler $jwtHandler,
		private EventDispatcherInterface $eventDispatcher,
	) {}

	/**
	 * @inheritDoc
	 */
	public function authenticate(ServerRequestInterface $request): ServerRequestInterface
	{
		if (!$request->hasHeader('Authorization')) {
			return $request;
		}

		try {
			$jwtToken = $this->jwtHandler->verifyRequest($request);
			$this->account = $this->authRepository
				->findByJwtToken($jwtToken)
				->withJwtToken($jwtToken);

			$this->eventDispatcher->dispatch(new AccountAuthenticated($this->account));

			return $request
				->withAttribute(RequestAttributeKeys::AUTHENTICATED, true)
				->withAttribute(self::ACCOUNT_KEY, $this->account);
		}
		catch (Throwable $e) {
			$this->eventDispatcher->dispatch(new AuthenticationFailed($request, $e));
			return $request;
		}
	}

	public function getAuthObject(): AuthObject
	{
		return $this->account ?? new NullAuthObject();
	}
}
