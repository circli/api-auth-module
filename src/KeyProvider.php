<?php declare(strict_types=1);

namespace Circli\ApiAuth;

use Lcobucci\JWT\Signer\Key;

interface KeyProvider
{
	/**
	 * @return list<Key>
	 */
	public function getKeys(): array;

	public function getPrimaryKey(): Key;
}
