<?php declare(strict_types=1);

namespace Circli\ApiAuth\Web\InputData;

use Circli\ApiAuth\Values\DeviceId;
use Circli\ApiAuth\Values\TokenInterface;

interface LoginDataInterface
{
	public function getToken(): TokenInterface;
	public function getDevice(): DeviceId;
}
