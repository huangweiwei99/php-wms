<?php

declare(strict_types=1);

namespace app\account\model;

use app\BaseModel;

/**
 * @mixin think\Model
 */
class Permission extends BaseModel
{
    protected $connection = 'account';
}
