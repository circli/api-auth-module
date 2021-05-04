<?php declare(strict_types=1);

namespace Circli\ApiAuth\Values;

use Sunkan\Enum\EnumInterface;

/**
 * @method static RoleInterface ADMIN()
 * @method static RoleInterface GUEST()
 * @method static RoleInterface ALL()
 * @method static RoleInterface ACCESS_KEY()
 */
interface RoleInterface extends EnumInterface
{
	public const ADMIN = 'admin';
	public const GUEST = 'guest';
	public const ALL = 'all';
	public const ACCESS_KEY = 'access_key';
}
