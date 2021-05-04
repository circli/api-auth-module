<?php declare(strict_types=1);

namespace Circli\ApiAuth\Repositories;

use Circli\ApiAuth\Entities\TokenAccount;
use Circli\ApiAuth\Exception\AccountNotFound;
use Circli\ApiAuth\Values\TokenInterface;
use Lcobucci\JWT\Token\Plain as JwtToken;

interface AuthRepositoryInterface
{
	/**
	 * @throws AccountNotFound
	 */
	public function findByJwtToken(JwtToken $jwtToken): TokenAccount;
	public function findByToken(TokenInterface $token): TokenAccount;
}
