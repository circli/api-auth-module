<?php

namespace Circli\ApiAuth\Tests\Provider;

use Circli\ApiAuth\Exception\InvalidArgument;
use Circli\ApiAuth\Provider\AuthProvider;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;
use Circli\ApiAuth\Provider\ApiAuthProvider;
use PHPUnit\Framework\TestCase;

class ApiAuthProviderTest extends TestCase
{
	public function testRedirectAuthentication(): void
	{
		$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhY2NvdW50SWQiOiIzNTQyNWRiMi0wYWNmLTQ0MzEtYjIxNC1hNDE1MDk3Y2UwMGQiLCJyb2xlIjoiYWRtaW4iLCJpc3MiOiJ0ZXN0IiwiaWF0IjoxNjIwMTQ2ODcyLjU5MzI0MiwiZXhwIjoxNjIwMTQ3MTcyLjU5MzI0MiwibG9jYXRpb24iOiIvcmFuZG9tL2xvY2F0aW9uIn0.4eWwe0DEM5DZ6SfGNlnMkCEC0tm3Z2B5a10oFc0ocEg';

		$keyProvider = $this->createMock(AuthProvider::class);
		$keyProvider->expects($this->once())->method('authenticate')->willThrowException(new InvalidArgument('test'));
		$tokenProvider = $this->createMock(AuthProvider::class);

		$provider = new ApiAuthProvider($keyProvider, $tokenProvider, new NullLogger());

		$request = $this->createMock(ServerRequestInterface::class);
		$request->expects($this->once())->method('hasHeader')->willReturn(false);
		$request->expects($this->once())->method('getQueryParams')->willReturn([
			ApiAuthProvider::REDIRECT_ACCESS_KEY => $token,
		]);
		$request->expects($this->once())->method('withHeader')->with(
			'Authorization',
			'Bearer ' . $token
		)->willReturn($request);

		$provider->authenticate($request);
	}

	public function testRedirectAuthenticationWithHeaderAuth(): void
	{
		$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhY2NvdW50SWQiOiIzNTQyNWRiMi0wYWNmLTQ0MzEtYjIxNC1hNDE1MDk3Y2UwMGQiLCJyb2xlIjoiYWRtaW4iLCJpc3MiOiJ0ZXN0IiwiaWF0IjoxNjIwMTQ2ODcyLjU5MzI0MiwiZXhwIjoxNjIwMTQ3MTcyLjU5MzI0MiwibG9jYXRpb24iOiIvcmFuZG9tL2xvY2F0aW9uIn0.4eWwe0DEM5DZ6SfGNlnMkCEC0tm3Z2B5a10oFc0ocEg';

		$keyProvider = $this->createMock(AuthProvider::class);
		$keyProvider->expects($this->never())->method('authenticate');
		$tokenProvider = $this->createMock(AuthProvider::class);

		$provider = new ApiAuthProvider($keyProvider, $tokenProvider, new NullLogger());

		$request = $this->createMock(ServerRequestInterface::class);
		$request->expects($this->once())->method('hasHeader')->willReturn(true);
		$request->expects($this->once())->method('getQueryParams')->willReturn([
			ApiAuthProvider::REDIRECT_ACCESS_KEY => $token,
		]);
		$request->expects($this->never())->method('withHeader');

		$provider->authenticate($request);
	}
}
