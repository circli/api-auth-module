<?php declare(strict_types=1);

namespace Circli\ApiAuth\Events;

use Circli\ApiAuth\Entities\TokenAccount;
use Circli\ApiAuth\Web\InputData\LoginDataInterface;

final class LoginSuccess
{
	public function __construct(
		private TokenAccount $tokenAccount,
		private LoginDataInterface $inputData,
	) {}

	public function getTokenAccount(): TokenAccount
	{
		return $this->tokenAccount;
	}

	public function setTokenAccount(TokenAccount $tokenAccount): void
	{
		$this->tokenAccount = $tokenAccount;
	}

	public function getInputData(): LoginDataInterface
	{
		return $this->inputData;
	}
}
