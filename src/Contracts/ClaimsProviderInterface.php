<?php declare(strict_types=1);

namespace Circli\ApiAuth\Contracts;

interface ClaimsProviderInterface
{
	public const ACCOUNT_ID = 'accountId';

	/**
	 * @return string[]
	 */
	public function getClaims(): array;
}
