<?php declare(strict_types=1);

namespace Circli\ApiAuth\Web\InputData;

use Circli\ApiAuth\Values\DeviceId;
use Circli\ApiAuth\Values\TokenInterface;

final class LoginData implements LoginDataInterface
{
	public function __construct(
		private TokenInterface $token,
		private DeviceId $device,
	) {}

	public function getToken(): TokenInterface
	{
		return $this->token;
	}

	public function getDevice(): DeviceId
	{
		return $this->device;
	}
}
