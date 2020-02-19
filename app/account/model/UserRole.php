<?php

declare(strict_types=1);

namespace app\Account\model;

use think\model\Pivot;

/**
 * @mixin think\Model
 */
class UserRole extends Pivot
{
    protected $table = 'userrole';
    // protected $autoWriteTimestamp = true;
}
