<?php declare(strict_types=1);

namespace Circli\ApiAuth\Repositories;

use Circli\ApiAuth\Entities\LocalAuthObject;
use Circli\ApiAuth\Repository\AccessKeyRepository;
use Circli\ApiAuth\Repository\Object\AccessKey;
use Circli\ApiAuth\Repository\Object\AuthToken;
use Circli\ApiAuth\Values\Role;
use Circli\ApiAuth\Values\StaticAccessKey;
use Circli\Extension\Auth\Repositories\Objects\AuthObject;

final class StaticAccessKeyProvider implements AccessKeyRepository
{
	/** @var array<string, array<string, string>> */
	private array $keys;

	/**
	 * @param array<int, array<string, string>> $keys
	 */
	public static function fromArray(array $keys): self
	{
		$newKeys = [];
		foreach ($keys as $key) {
			$newKeys[$key['id']] = $key;
		}

		return new self($newKeys);
	}

	/**
	 * @param array<array<string, string>> $keys
	 */
	public function __construct(array $keys)
	{
		$this->keys = $keys;
	}

	public function findByApiKey(string $apiId): ?AuthToken
	{
		if (!isset($this->keys[$apiId])) {
			return null;
		}

		return new StaticAccessKey($this->keys[$apiId]['id'], $this->keys[$apiId]['token']);
	}

	public function createAuthObject(AuthToken $token): AuthObject
	{
		$data = [];
		if ($token instanceof AccessKey) {
			$data = $this->keys[$token->getKey()] ?? [];
		}
		if (!isset($data['role'])) {
			return new LocalAuthObject(Role::fromValue('guest'));
		}

		return new LocalAuthObject(Role::fromValue($data['role']));
	}
}
