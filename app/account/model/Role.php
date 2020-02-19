<?php

declare(strict_types=1);

namespace app\account\model;

use app\BaseModel;

/**
 * @mixin think\Model
 */
class Role extends BaseModel
{
    protected $connection = 'account';

    // 设置当前模型对应的完整数据表名称
    protected $table = 't_role';

    /**
     * 角色标题搜索器.
     */
    public function searchTitleAttr($query, $value, $data)
    {
        $query->where('title', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    /**
     * 用户角色多对多.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, UserRole::class, 'user_id', 'role_id');
    }
}
