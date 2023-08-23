<?php

declare(strict_types=1);

namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * Class UserStatus
 *
 * @method static self pending()
 * @method static self enabled()
 * @method static self disabled()
 *
 * @package App\Enums
 */
final class UserStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'pending' => 1,
            'enabled' => 2,
            'disabled' => 3,
        ];
    }

    /**
     * @return array
     */
    protected static function labels(): array
    {
        return [
            'pending' => __('enum.user.status.pending'),
            'enabled' => __('enum.user.status.enabled'),
            'disabled' => __('enum.user.status.disabled'),
        ];
    }
}
