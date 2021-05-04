<?php declare(strict_types=1);

namespace Circli\ApiAuth\Web\Input;

use Circli\ApiAuth\Exception\InvalidInput;
use Circli\ApiAuth\Values\DeviceId;
use Circli\ApiAuth\Values\Token;
use Circli\ApiAuth\Web\InputData\LoginData;
use Circli\ApiAuth\Web\InputData\LoginDataInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

final class LoginInput implements LoginInputInterface
{
	public function __invoke(ServerRequestInterface $request): LoginDataInterface
	{
		//prevent timing attacks
		usleep(random_int(25, 100));

		/** @var array<string, mixed> $body */
		$body = $request->getParsedBody();
		$deviceId = null;
		if (isset($body['deviceUuid']) || isset($body['device_uuid'])) {
			$deviceId = DeviceId::fromUuid(Uuid::fromString($body['deviceUuid'] ?? $body['device_uuid']));
		}

		if (!$deviceId instanceof DeviceId) {
			throw InvalidInput::deviceId();
		}

		if (!isset($body['id'], $body['password'])) {
			throw InvalidInput::missingRequiredData();
		}

		$token = Token::fromId($body['id'], $body['password']);

		return new LoginData($token, $deviceId);
	}
}
