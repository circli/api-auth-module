<?php declare(strict_types=1);

namespace Circli\ApiAuth\Values;

use Ramsey\Uuid\UuidInterface;

final class DeviceId
{
	private UuidInterface $uuid;

	public static function fromUuid(UuidInterface $uuid): self
	{
		return new self($uuid);
	}

	private function __construct(UuidInterface $uuid)
	{
		$this->uuid = $uuid;
	}

	public function toString(): string
	{
		return $this->uuid->toString();
	}
}
