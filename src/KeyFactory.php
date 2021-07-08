<?php declare(strict_types=1);

namespace Circli\ApiAuth;

use Circli\Core\Config;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;

final class KeyFactory
{
	public function create(Config $config): Key
	{
		return InMemory::base64Encoded($config->get('jwt.secret'));
	}
}
