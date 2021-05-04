<?php declare(strict_types=1);

namespace Circli\ApiAuth\Entities;

use Circli\ApiAuth\Contracts\ClaimsProviderInterface;
use Circli\ApiAuth\Values\AccountId;
use Circli\ApiAuth\Values\RoleInterface;
use Circli\ApiAuth\Values\TokenInterface;
use Circli\Extension\Auth\Repositories\Objects\AuthObject;
use Lcobucci\JWT\Token\Plain as JwtToken;

final class TokenAccount implements \JsonSerializable, AuthObject, ClaimsProviderInterface
{
	private ?JwtToken $jwtToken;
	/** @var string[] */
	private array $claims = [];

	public function __construct(
		private Account $account,
		private TokenInterface $password,
	) {}

	public function isActive(): bool
	{
		return true;
	}

	public function getAccount(): Account
	{
		return $this->account;
	}

	public function getToken(): TokenInterface
	{
		return $this->password;
	}

	public function withJwtToken(JwtToken $token): self
	{
		$self = clone $this;
		$self->jwtToken = $token;
		return $self;
	}

	public function withClaim(string $key, string $value): self
	{
		$self = clone $this;
		$self->claims[$key] = $value;
		return $self;
	}

	public function getJwtToken(): ?JwtToken
	{
		return $this->jwtToken;
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize()
	{
		return $this->account;
	}

	public function getAccountId(): AccountId
	{
		return $this->account->getId();
	}

	public function getRole(): RoleInterface
	{
		return $this->account->getRole();
	}

	public function getClaims(): array
	{
		return array_merge($this->account->getClaims(), $this->claims);
	}
}
