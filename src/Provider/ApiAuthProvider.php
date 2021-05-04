<?php declare(strict_types=1);

namespace Circli\ApiAuth\Provider;

use Circli\ApiAuth\Entities\ApiAuthObject;
use Circli\ApiAuth\Entities\TokenAccount;
use Circli\ApiAuth\Exception\InvalidArgument;
use Circli\ApiAuth\Exception\NotAuthenticated;
use Circli\Extension\Auth\Repositories\Objects\AuthObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class ApiAuthProvider implements AuthProvider
{
	public const REDIRECT_ACCESS_KEY = 'rak';
	public const WEBHOOK_ACCESS_KEY = 'wak';

	public function __construct(
		private AuthProvider $apiAccessProvider,
		private AuthProvider $userAccessProvider,
		private LoggerInterface $logger,
	) {}

	/**
	 * @inheritDoc
	 */
	public function authenticate(ServerRequestInterface $request): ServerRequestInterface
	{
		$throwOnNoAccount = false;
		$query = $request->getQueryParams();
		$queryStringAuthentication = isset($query[self::REDIRECT_ACCESS_KEY]);
		$webhookAuthentication = isset($query[self::WEBHOOK_ACCESS_KEY]);
		$hasAuthorizationHeader = $request->hasHeader('Authorization');

		try {
			if (!$hasAuthorizationHeader) {
				$request = $this->apiAccessProvider->authenticate($request);
				$this->logger->debug('Authenticated with api-key');
			}
		}
		catch (InvalidArgument $e) {
			$this->logger->debug('Failed authenticated with api-key', [
				'exception' => $e,
			]);
			if (!$webhookAuthentication && !$queryStringAuthentication) {
				throw $e;
			}
			$throwOnNoAccount = true;
		}

		if (!$hasAuthorizationHeader && $queryStringAuthentication) {
			$this->logger->debug('Setting Bearer from rak');
			$request = $request->withHeader('Authorization', 'Bearer ' . $query[self::REDIRECT_ACCESS_KEY]);
		}
		elseif (!$hasAuthorizationHeader && $webhookAuthentication) {
			$this->logger->debug('Setting Bearer from wak');
			$request = $request->withHeader('Authorization', 'Bearer ' . $query[self::WEBHOOK_ACCESS_KEY]);
		}

		try {
			$request = $this->userAccessProvider->authenticate($request);
			$this->logger->debug('Bearer Token authenticated');
			if ($hasAuthorizationHeader) {
				return $request;
			}
			if (!$queryStringAuthentication) {
				return $request;
			}
			$authObject = $this->userAccessProvider->getAuthObject();
			if (!$authObject instanceof TokenAccount) {
				return $request;
			}
			$token = $authObject->getJwtToken();
			if (!$token) {
				// this should not be possible
				return $request;
			}

			if (!$token->claims()->has('location')) {
				throw new NotAuthenticated('Querystring based bearer token must contain location');
			}

			$authPath = $token->claims()->get('location');
			$currentPath = $request->getUri()->getPath();
			if ($authPath !== $currentPath) {
				throw new NotAuthenticated('Bearer token not valid for uri');
			}
		}
		catch (NotAuthenticated $e) {
			$this->logger->debug('Failed Bearer Token authenticated');
			if ($throwOnNoAccount) {
				$this->logger->notice('No authentication succeeded');
				throw $e;
			}
		}

		return $request;
	}

	public function getAuthObject(): AuthObject
	{
		return new ApiAuthObject(
			$this->apiAccessProvider->getAuthObject(),
			$this->userAccessProvider->getAuthObject()
		);
	}
}
