<?php declare(strict_types=1);

namespace Circli\ApiAuth;

use Lcobucci\JWT\Signer\Key;

final class KeyProviderCollection implements KeyProvider
{
	/**
	 * @param list<Key> $keys
	 */
	public function __construct(
		private Key $primaryKeys,
		private array $keys = [],
	) {}

	/**
	 * @inheritDoc
	 */
	public function getKeys(): array
	{
		return $this->keys;
	}

	public function getPrimaryKey(): Key
	{
		return $this->primaryKeys;
	}
}
