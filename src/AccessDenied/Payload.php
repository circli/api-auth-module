<?php declare(strict_types=1);

namespace Circli\ApiAuth\AccessDenied;

use PayloadInterop\DomainPayload;
use PayloadInterop\DomainStatus;

final class Payload implements DomainPayload
{
	/** @var array<string, mixed> */
	private array $result;

	public static function fromDomainPayload(DomainPayload $payload): self
	{
		$result = $payload->getResult();
		if (!isset($result['messages'])) {
			$result['messages'] = 'Access denied';
			$result['code'] = 'ACCESS_DENIED';
		}
		return new self($result);
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public function __construct(array $data)
	{
		$this->result = $data;
	}

	public function getStatus(): string
	{
		return DomainStatus::UNAUTHORIZED;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getResult(): array
	{
		return $this->result;
	}
}
