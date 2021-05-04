<?php declare(strict_types=1);

namespace Circli\ApiAuth\AccessDenied;

use PayloadInterop\DomainPayload;
use Polus\Adr\Interfaces\Responder as ResponderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class Responder implements ResponderInterface
{
	public function __construct(
		private ResponderInterface $responder,
	) {}

	public function __invoke(
		ServerRequestInterface $request,
		ResponseInterface $response,
		DomainPayload $payload,
	): ResponseInterface {
		return $this->responder->__invoke($request, $response, Payload::fromDomainPayload($payload));
	}
}
