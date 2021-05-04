<?php declare(strict_types=1);

namespace Circli\ApiAuth\Events;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class AuthenticationFailed
{
	public function __construct(
		private ServerRequestInterface $request,
		private Throwable $exception,
	) {}

	public function getRequest(): ServerRequestInterface
	{
		return $this->request;
	}

	public function getException(): Throwable
	{
		return $this->exception;
	}
}
