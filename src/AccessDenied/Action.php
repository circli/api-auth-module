<?php declare(strict_types=1);

namespace Circli\ApiAuth\AccessDenied;

use Circli\Extension\Auth\Web\AccessDeniedActionInterface;
use Circli\WebCore\Common\Actions\AbstractAction;

final class Action extends AbstractAction implements AccessDeniedActionInterface
{
	protected ?string $responder = Responder::class;
}
