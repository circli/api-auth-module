<?php declare(strict_types=1);

namespace Circli\ApiAuth\Exception;

use Circli\ApiAuth\Contracts\ClaimsProviderInterface;

final class InvalidInput extends \InvalidArgumentException
{
	public static function deviceId(): self
	{
		return new self('Invalid device uuid');
	}

	public static function missingRequiredData(): self
	{
		return new self('Missing authentication data');
	}

	public static function missingAccountId(): self
	{
		return new self("Token doesn't contain " . ClaimsProviderInterface::ACCOUNT_ID);
	}
}
