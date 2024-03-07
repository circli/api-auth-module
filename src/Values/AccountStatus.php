<?php declare(strict_types=1);

namespace Circli\ApiAuth\Values;

use Sunkan\Enum\EnumClass;

/**
 * @method static static ACTIVE()
 * @method static static INACTIVE()
 */
class AccountStatus extends EnumClass
{
	public const ACTIVE = 'active';
	public const INACTIVE = 'inactive';
}
