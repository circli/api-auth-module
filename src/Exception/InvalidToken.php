<?php declare(strict_types=1);

namespace Circli\ApiAuth\Exception;

use Lcobucci\JWT\Token\Plain as Token;

final class InvalidToken extends \RuntimeException
{
	public static function signature(): self
	{
		return new self('Jwt token signature not valid');
	}

	public static function type(): \InvalidArgumentException
	{
		return new \InvalidArgumentException('Parsed token should be of type "' . Token::class .'"');
	}
}
