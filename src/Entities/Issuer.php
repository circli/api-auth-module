<?php declare(strict_types=1);

namespace Circli\ApiAuth\Entities;

final class Issuer
{
	public function __construct(
		private string $host,
	) {}

	public function getHost(): string
	{
		return $this->host;
	}
}
