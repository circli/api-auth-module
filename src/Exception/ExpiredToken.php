<?php declare(strict_types=1);

namespace Circli\ApiAuth\Exception;

use Throwable;

final class ExpiredToken extends \RuntimeException
{
	public function __construct(string $message = 'Jwt token expired')
	{
		parent::__construct($message);
	}
}
