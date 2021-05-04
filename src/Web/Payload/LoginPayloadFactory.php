<?php declare(strict_types=1);

namespace Circli\ApiAuth\Web\Payload;

use Circli\ApiAuth\Entities\TokenAccount;
use Lcobucci\JWT\Token;
use PayloadInterop\DomainPayload;

interface LoginPayloadFactory
{
	public function success(TokenAccount $account, Token $accessToken): DomainPayload;
	public function accountNotActive(TokenAccount $account): DomainPayload;
	public function failure(\Throwable $exception): DomainPayload;
}
