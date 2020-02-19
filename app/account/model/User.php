<?php

declare(strict_types=1);

namespace app\account\model;

use app\Account\model\UserRole;
use app\BaseModel;
use Ozh\Phpass\PasswordHash;

/**
 * @mixin think\Model
 */
class User extends BaseModel
{
    protected $connection = 'account';
    // 设置当前模型对应的完整数据表名称
    protected $table = 't_user';

    /**
     * 密码字段修改器.
     *
     * @param string $value 外界输入的密码
     *
     * @return string sha1(md5($value)) 加密后的密码
     */
    public function setPasswordAttr($value)
    {
        $hasher = new PasswordHash(8, true);
        // 执行加密
        $hasher_password = $hasher->HashPassword($value);

        return $hasher_password;
    }

    /**
     * 用户名搜索器.
     */
    public function searchUsernameAttr($query, $value, $data)
    {
        $query->where('username', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    /**
     * 用户名���索器.
     */
    public function searchEmailAttr($query, $value, $data)
    {
        $query->where('email', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    /**
     * 电话搜索器.
     */
    public function searchPhoneAttr($query, $value, $data)
    {
        $query->where('phone', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    /**
     * 用户角色多对多.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, UserRole::class, 'role_id', 'user_id');
    }

    /**
     * 给用户增加管理员权限 会自动写入角色表和中间表数据.
     *
     * @param int   $id    用户ID
     * @param array $roles 角色属性数组
     *
     * @return
     */
    public function saveRoles($id, array $roles)
    {
        if (1 === count($roles)) {
            $data = $this->find($id)->roles()->save($roles);
        } else {
            // 批量授权
            $data = $this->find($id)->roles()->saveAll($roles);
        }

        return $data;
    }

    /**
     * 单独更新中间表数据.
     *
     * @param int   $id       用户ID
     * @param array $role_ids 角色id数组
     *
     * @return Pivot 对象实例/实例数组
     */
    public function attachRoles($id, array $role_ids, array $extra = null)
    {
        if (is_null($extra)) {
            $data = $this->find($id)->roles()->attach($role_ids);
        } else {
            // 传入中间表的额外属性
            $data = $this->find($id)->roles()->attach($role_ids, $extra);
        }

        return $data;
    }

    /**
     * 删除中间表数据.
     *
     * @param int   $id       用户ID
     * @param array $role_ids 角色id数组
     *
     * @return
     */
    public function detachRoles($id, array $role_ids)
    {
        return $this->find($id)->roles()->detach($role_ids);
    }
}
