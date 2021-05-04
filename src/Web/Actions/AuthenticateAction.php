<?php declare(strict_types=1);

namespace Circli\ApiAuth\Web\Actions;

use Circli\ApiAuth\Web\Domain\Login;
use Circli\ApiAuth\Web\Input\LoginInputInterface;
use Polus\Adr\Actions\AbstractDomainAction;

final class AuthenticateAction extends AbstractDomainAction
{
	protected ?string $input = LoginInputInterface::class;
	protected ?string $domain = Login::class;
}
