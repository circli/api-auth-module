<?php declare(strict_types=1);

namespace Circli\ApiAuth\Web\Payload;

use Circli\ApiAuth\Entities\TokenAccount;
use Lcobucci\JWT\Token as JWTToken;
use PayloadInterop\DomainPayload;

final class DefaultPayloadFactory implements LoginPayloadFactory
{
	public function success(TokenAccount $account, JWTToken $accessToken): DomainPayload
	{
		return LoginPayload::SUCCESS($account, $accessToken);
	}

	public function accountNotActive(TokenAccount $account): DomainPayload
	{
		return LoginPayload::ACCOUNT_NOT_ACTIVE();
	}

	public function failure(\Throwable $exception): DomainPayload
	{
		return LoginPayload::FAILURE();
	}
}
