<?php declare(strict_types=1);

namespace Circli\ApiAuth\Values;

use Circli\ApiAuth\Contracts\ClaimsProviderInterface;
use Circli\ApiAuth\Exception\InvalidInput;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AccountId implements \JsonSerializable
{
	public static function fromJwtToken(\Lcobucci\JWT\UnencryptedToken $token): self
	{
		if (!$token->claims()->has(ClaimsProviderInterface::ACCOUNT_ID)) {
			throw InvalidInput::missingAccountId();
		}
		$accountId = $token->claims()->get(ClaimsProviderInterface::ACCOUNT_ID);
		if (Uuid::isValid($accountId)) {
			return new static(0, Uuid::fromString($accountId));
		}

		return new static((int)$accountId, null);
	}

	public static function new(UuidInterface $accountUuid = null): self
	{
		$accountUuid = $accountUuid ?? Uuid::uuid4();
		return new static(0, $accountUuid);
	}

	public static function fromDb(string|int $accountId, UuidInterface $accountUuid = null): self
	{
		return new static((int)$accountId, $accountUuid);
	}

	final protected function __construct(
		private int $id,
		private ?UuidInterface $uuid,
	) {}

	public function toString(): string
	{
		return $this->uuid?->toString() ?? (string)$this->id;
	}

	public function isUuid(): bool
	{
		return $this->uuid instanceof UuidInterface;
	}

	public function getUuid(): UuidInterface
	{
		if (!$this->uuid) {
			throw new \BadMethodCallException('No uuid found');
		}
		return $this->uuid;
	}

	public function toInt(): int
	{
		return $this->id;
	}

	public function toBytes(): string
	{
		return $this->uuid?->getBytes() ?? '';
	}

	public function jsonSerialize()
	{
		return $this->toString();
	}
}
