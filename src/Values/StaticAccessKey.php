<?php declare(strict_types=1);

namespace Circli\ApiAuth\Values;

use Circli\ApiAuth\Repository\Object\AuthToken;

final class StaticAccessKey implements AuthToken
{
	public function __construct(
		private string $id,
		private string $token,
	) {}

	public function getId(): string
	{
		return $this->id;
	}

	public function isValid(string $apiToken): bool
	{
		return hash_equals($this->token, $apiToken);
	}
}
