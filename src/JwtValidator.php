<?php declare(strict_types=1);

namespace Circli\ApiAuth;

use Circli\ApiAuth\Entities\Issuer;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\Validator;

final class JwtValidator implements \Lcobucci\JWT\Validator
{
	/** @var Constraint[] */
	private array $constraints;

	public function __construct(
		private Validator $validator,
		Issuer $issuer
	) {
		$this->constraints = [
			new Constraint\IssuedBy($issuer->getHost()),
			new Constraint\LooseValidAt(new SystemClock(new \DateTimeZone('UTC'))),
		];
	}

	public function assert(Token $token, Constraint ...$constraints): void
	{
		$this->validator->assert($token, ...$constraints, ...$this->constraints);
	}

	public function validate(Token $token, Constraint ...$constraints): bool
	{
		return $this->validator->validate($token, ...$constraints, ...$this->constraints);
	}
}
