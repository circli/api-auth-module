<?php declare(strict_types=1);

namespace Circli\ApiAuth\Values;

use Circli\ApiAuth\Exception\InvalidToken;

final class Token implements TokenInterface
{
	public static function fromToken(string $provider, string $key, string $value): self
	{
		return new self($provider, $key, $value);
	}

	public static function fromId(string $id, string $password): self
	{
		return new self('password', $id, $password);
	}

	public static function new(string $provider): self
	{
		$id = bin2hex(random_bytes(8));
		$token = bin2hex(random_bytes(16));

		return new self($provider, $id, $token);
	}

	private function __construct(
		private string $provider,
		private string $key,
		private string $value,
	) {}

	public function getProvider(): string
	{
		return $this->provider;
	}

	public function getKey(): string
	{
		return $this->key;
	}

	public function getValue(): string
	{
		return $this->value;
	}

	public function toString(): string
	{
		return $this->key . '.' . $this->value;
	}

	public function isValid(TokenInterface $token): bool
	{
		if (str_starts_with($this->value, '$')) {
			if (!password_verify($token->getValue(), $this->value)) {
				throw new InvalidToken('Invalid token');
			}
		}
		elseif (!hash_equals($this->value, $token->getValue())) {
			throw new InvalidToken('Invalid token');
		}
		return true;
	}
}
