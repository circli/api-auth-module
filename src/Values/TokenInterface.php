<?php declare(strict_types=1);

namespace Circli\ApiAuth\Values;

interface TokenInterface
{
	public function getProvider(): string;

	public function getKey(): string;

	public function getValue(): string;

	public function toString(): string;

	public function isValid(TokenInterface $token): bool;
}
