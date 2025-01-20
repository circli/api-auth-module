<?php declare(strict_types=1);

namespace Circli\ApiAuth;

use Circli\ApiAuth\Contracts\ClaimsProviderInterface;
use Circli\ApiAuth\Entities\Issuer;
use Circli\ApiAuth\Exception\ExpiredToken;
use Circli\ApiAuth\Exception\InvalidToken;
use DateTimeImmutable;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Plain as Token;
use Lcobucci\JWT\Token\RegisteredClaimGiven;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validator;
use Psr\Clock\ClockInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class JwtHandler
{
	public const CLAIM_LOCATION = 'location';

	private Key $primaryKey;

	public function __construct(
		private Key|KeyProvider $keys,
		private Issuer $issuer,
		private Signer $signer,
		private Validator $validator,
		private ClockInterface $clock,
		private LoggerInterface $logger,
	) {
		if ($this->keys instanceof Key) {
			$this->primaryKey = $this->keys;
		}
		else {
			$this->primaryKey = $this->keys->getPrimaryKey();
		}
	}

	public function createBearerToken(ClaimsProviderInterface $claims, \DateInterval|null $expire = null): Token
	{
		$now = new DateTimeImmutable();
		$builder = new Builder(new JoseEncoder(), ChainedFormatter::default());
		$builder->issuedBy($this->issuer->getHost());
		$builder->issuedAt($now);
		if ($expire !== null) {
			$builder->expiresAt($now->add($expire));
		}
		foreach ($claims->getClaims() as $key => $value) {
			$builder->withClaim($key, $value);
		}
		return $builder->getToken($this->signer, $this->primaryKey);
	}

	public function createQueryAccessTokenFromRequest(string $location, RequestInterface $request): Token
	{
		$authHeader = $request->getHeaderLine('Authorization');
		[, $jwt] = explode(' ', $authHeader);
		$token = (new Parser(new JoseEncoder()))->parse($jwt);
		if (!$token instanceof Token) {
			throw InvalidToken::type();
		}

		return $this->createQueryAccessToken($location, $token);
	}

	public function createQueryAccessToken(string $location, Token $bearerToken): Token
	{
		$now = new DateTimeImmutable();
		$builder = new Builder(new JoseEncoder(), ChainedFormatter::default());
		foreach ($bearerToken->claims()->all() as $key => $value) {
			try {
				$builder->withClaim($key, $value);
			}
			catch (RegisteredClaimGiven) {}
		}

		$builder->issuedBy($this->issuer->getHost());
		$builder->issuedAt($now);
		$builder->expiresAt($now->add(new \DateInterval('PT5M')));
		$builder->withClaim(self::CLAIM_LOCATION, $location);

		return $builder->getToken($this->signer, $this->primaryKey);
	}

	public function verifyRequest(ServerRequestInterface $request): Token
	{
		$authHeader = $request->getHeaderLine('Authorization');
		[, $bearer] = explode(' ', $authHeader, 2);
		$jwtToken = (new Parser(new JoseEncoder()))->parse($bearer);
		if (!$jwtToken instanceof Token) {
			throw InvalidToken::type();
		}

		if ($jwtToken->isExpired($this->clock->now())) {
			throw new ExpiredToken();
		}

		$signed = new SignedWith($this->signer, $this->primaryKey);
		if ($this->validator->validate($jwtToken, $signed)) {
			return $jwtToken;
		}

		if ($this->keys instanceof KeyProvider) {
			foreach ($this->keys->getKeys() as $key) {
				$signed = new SignedWith($this->signer, $key);
				if ($this->validator->validate($jwtToken, $signed)) {
					$this->logger->notice('Token using old key', [
						'token' => $jwtToken->toString(),
					]);
					return $jwtToken;
				}
			}
		}

		throw InvalidToken::signature();
	}
}
