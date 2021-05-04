<?php declare(strict_types=1);

namespace Circli\ApiAuth\Web\Input;

use Circli\ApiAuth\Web\InputData\LoginDataInterface;
use Psr\Http\Message\ServerRequestInterface;

interface LoginInputInterface
{
	public function __invoke(ServerRequestInterface $request): LoginDataInterface;
}
