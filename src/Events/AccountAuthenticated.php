<?php declare(strict_types=1);

namespace Circli\ApiAuth\Events;

use Circli\ApiAuth\Entities\TokenAccount;

final class AccountAuthenticated
{
	public function __construct(
		private TokenAccount $account,
	) {}

	public function getAccount(): TokenAccount
	{
		return $this->account;
	}
}
