<?php declare(strict_types=1);

namespace Circli\ApiAuth\Values;

use Sunkan\Enum\EnumClass;

/**
 * @method static Role ADMIN()
 * @method static Role GUEST()
 * @method static Role ALL()
 * @method static Role ACCESS_KEY()
 */
class Role extends EnumClass implements RoleInterface
{
}
