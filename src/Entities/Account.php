<?php declare(strict_types=1);

namespace Circli\ApiAuth\Entities;

use Circli\ApiAuth\Contracts\ClaimsProviderInterface;
use Circli\ApiAuth\Values\AccountId;
use Circli\ApiAuth\Values\AccountStatus;
use Circli\ApiAuth\Values\Role;
use Circli\ApiAuth\Values\RoleInterface;

abstract class Account implements \JsonSerializable, ClaimsProviderInterface
{
	public function __construct(
		private AccountId $id,
		private string $name,
		private AccountStatus $status,
		private ?RoleInterface $role = null,
	) {}

	public function getStatus(): AccountStatus
	{
		return $this->status;
	}

	public function getId(): AccountId
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(): mixed
	{
		return get_object_vars($this);
	}

	public function getRole(): RoleInterface
	{
		return $this->role ?? Role::GUEST();
	}
}
