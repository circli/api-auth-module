<?php declare(strict_types=1);

namespace Circli\ApiAuth\Web\Payload;

use Circli\ApiAuth\Entities\TokenAccount;
use Circli\WebCore\Common\Payload\AbstractPayload;
use Circli\WebCore\DomainStatus;
use Lcobucci\JWT\Token as JWTToken;
use PayloadInterop\DomainPayload;

/**
 * @method static static ACCOUNT_NOT_ACTIVE()
 * @method static static FAILURE()
 */
final class LoginPayload extends AbstractPayload
{
	public const SUCCESS = DomainStatus::SUCCESS;
	public const ACCOUNT_NOT_ACTIVE = DomainStatus::INVALID;
	public const FAILURE = DomainStatus::ERROR;

	protected const ALLOWED_STATUS = [
		self::SUCCESS,
		self::ACCOUNT_NOT_ACTIVE,
		self::FAILURE,
	];

	protected const MESSAGES = [
		self::SUCCESS => 'Account have been authenticated',
		self::FAILURE => 'Failed to authenticate account',
		self::ACCOUNT_NOT_ACTIVE => 'Account is not active',
	];

	public static function SUCCESS(TokenAccount $account, JWTToken $accessToken): DomainPayload
	{
		$self = new self(self::SUCCESS);
		$self->result = [
			'account' => $account,
			'accessToken' => $accessToken->toString(),
		];

		return $self;
	}
}
