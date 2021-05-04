<?php declare(strict_types=1);

namespace Circli\ApiAuth\Web\Domain;

use Circli\ApiAuth\Events\LoginSuccess;
use Circli\ApiAuth\Exception\AccountNotFound;
use Circli\ApiAuth\Exception\ExpiredToken;
use Circli\ApiAuth\Exception\InvalidToken;
use Circli\ApiAuth\JwtHandler;
use Circli\ApiAuth\Repositories\AuthRepositoryInterface;
use Circli\ApiAuth\Web\InputData\LoginDataInterface;
use Circli\ApiAuth\Web\Payload\LoginPayloadFactory;
use PayloadInterop\DomainPayload;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

final class Login
{
	public function __construct(
		private AuthRepositoryInterface $repository,
		private JwtHandler $jwtHandler,
		private LoginPayloadFactory $payloadFactory,
		private EventDispatcherInterface $eventDispatcher,
		private LoggerInterface $logger,
	) {}

	public function __invoke(LoginDataInterface $data): DomainPayload
	{
		$token = $data->getToken();
		try {
			$tokenAccount = $this->repository->findByToken($token);
			$tokenAccount->getToken()->isValid($token);

			$tokenAccount = $this->eventDispatcher->dispatch(
				new LoginSuccess($tokenAccount, $data)
			)->getTokenAccount();

			if ($tokenAccount->isActive()) {
				return $this->payloadFactory->success(
					$tokenAccount,
					$this->jwtHandler->createBearerToken($tokenAccount)
				);
			}
			return $this->payloadFactory->accountNotActive($tokenAccount);
		}
		catch (AccountNotFound $e) {
			$this->logger->warning('Account not found', [
				'provider' => $token->getProvider(),
				'key' => $token->getKey(),
			]);
			return $this->payloadFactory->failure($e);
		}
		catch (InvalidToken $e) {
			$this->logger->warning('Invalid token value', [
				'provider' => $token->getProvider(),
				'key' => $token->getKey(),
			]);
			return $this->payloadFactory->failure($e);
		}
		catch (ExpiredToken $e) {
			$this->logger->warning('Token have expired', [
				'provider' => $token->getProvider(),
				'key' => $token->getKey(),
			]);
			return $this->payloadFactory->failure($e);
		}
	}
}
