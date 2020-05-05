<?php

namespace app\service;

use app\account\facade\Permission;
use app\account\facade\Role;
use app\account\facade\User;

class Account
{
    //############################# User  #############################

    /**
     * 以ID获取单个用户.
     *
     * @param int $id 用户ID
     *
     * @return app\account\Model\User User 模型类
     */
    public function getUserById($id)
    {
        validate(['id' => 'integer'])->check(['id' => $id]);
        $user = User::getDataById($id);

        return $user;
    }

    public function getUserByName($username)
    {
        $user = User::getByUsername($username);

        return $user;
    }

    /**
     * 用户名和密码获取用户.
     *
     * @param string $username 用户名
     * @param string $password 密码
     *
     * @return app\account\Model\User User 模型类
     */
    public function getUserByUsrAndPwd($username, $password)
    {
        $field = [
                ['username' => $username],
                ['password' => sha1(md5($password))],
            ];

        return User::getDataByField($field);
    }

    /**
     * Users 获取多个用户.
     *
     * @param array  $keywords 字段数组
     * @param int    $page     页码
     * @param int    $limit    每页数量
     * @param string $sort     排序字段
     * @param string $order    序列 desc/asc
     *
     * @return array app\account\Model\User User模型类数据
     */
    public function getUsers($keywords = null, $page = null, $limit = null, $sort = null, $order = null)
    {
        $users = User::getDataCollection($keywords, $page, $limit, $sort, $order);
        if (isset($users->error)) {
            $this->error = $users->error;
        }

        return $users;
    }

    /**
     * 创建单个用户.
     *
     * @param array $params 参数属性
     *
     * @return app\account\Model\User User 模型类
     */
    public function createUser($params)
    {
        validate(User::class)->check($params);

        $user = User::where('username', $params['username'])->findOrEmpty();

        if (!$user->isEmpty()) {
            throw new \think\exception\ValidateException('用户名已经存在,重新填写');
        }

        return User::createData($params);
    }

    /**
     * 更新单个用户.
     *
     * @param array $params 参数属性
     * @param int   $id     用户ID
     *
     * @return app\account\Model\User User 模型类
     */
    public function updateUserById($params, $id)
    {
        validate(User::class)->check(array_merge($params, ['id' => $id]));

        return User::updateDataById($params, $id);
    }

    /**
     * 删除单个用户.
     *
     * @param int $id 用户ID
     *
     * @return bool|app\account\Model\User true|User 布尔值|模型类
     */
    public function deleteUserById($id)
    {
        validate(['id' => 'integer'])->check(['id' => $id]);

        return User::deleteDataById($id);
    }

    //############################# Role #############################

    /**
     * 以ID获取单个角色.
     *
     * @param int $id 角色ID
     *
     * @return app\account\Model\Role Role 模型类
     */
    public function getRoleById($id)
    {
        validate(['id' => 'integer'])->check(['id' => $id]);
        $role = Role::getDataById($id);

        return $role;
    }

    /**
     * Roles 获取多个角色.
     *
     * @param array  $keywords 字段数组
     * @param int    $page     页码
     * @param int    $limit    每页数量
     * @param string $sort     排序字段
     * @param string $order    序列 desc/asc
     *
     * @return array app\account\Model\Role Role模型类数据集
     */
    public function getRoles($keywords = null, $page = null, $limit = null, $sort = null, $order = null)
    {
        $roles = Role::getDataCollection($keywords, $page, $limit, $sort, $order);

        if (isset($roles->error)) {
            $this->error = $roles->error;
        }

        return $roles;
    }

    /**
     * 创建单个角色.
     *
     * @param array $params 参数属性
     *
     * @return app\account\Model\Role Role 模型类
     */
    public function createRole($params)
    {
        validate(Role::class)->check($params);

        return Role::createData($params);
    }

    /**
     * 更新单个角色.
     *
     * @param array $params 参数属性
     * @param int   $id     角色ID
     *
     * @return app\account\Model\Role Role 模型类
     */
    public function updateRoleById($params, $id)
    {
        validate(Role::class)->check(array_merge($params, ['id' => $id]));

        return Role::updateDataById($params, $id);
    }

    /**
     * 删除单个角色.
     *
     * @param int $id 角色ID
     *
     * @return bool|app\account\Model\Role true|Role 布尔值|模型类
     */
    public function deleteRoleById($id)
    {
        validate(['id' => 'integer'])->check(['id' => $id]);

        return Role::deleteDataById($id);
    }

    //############################# Permission #############################

    /**
     * 获取权限列表.
     *
     * @param array  $keywords 字段数组
     * @param int    $page     页码
     * @param int    $limit    每页数量
     * @param string $sort     排序字段
     * @param string $order    序列 desc/asc
     *
     * @return array app\account\Model\User User模型类数据
     */
    public function getPermissionList($keywords = null, $page = null, $limit = null, $sort = null, $order = null)
    {
        $permission_conllection = Permission::getDataCollection($keywords, $page, $limit, $sort, $order);
        if (isset($permission_conllection->error)) {
            $this->error = $permission_conllection->error;
        }

        return $permission_conllection;
    }

    /**
     * 创建权限.
     *
     * @param array $params 权限参数属性
     *
     * @return app\account\Model\Permission Permission 模型类
     */
    public function createPermission($params)
    {
        validate(Permission::class)->check($params);

        return Permission::createData($params);
    }

    /**
     * 更新单个权限.
     *
     * @param array $params 权限参数属性
     * @param int   $id     权限ID
     *
     * @return app\account\Model\Permission Permission 模型类
     */
    public function updatePermissionById($params, $id)
    {
        validate(Permission::class)->check(array_merge($params, ['id' => $id]));

        return Permission::updateDataById($params, $id);
    }

    /**
     * 以ID获取权限.
     *
     * @param int $id 权限ID
     *
     * @return app\account\Model\Permission Permission 模型类
     */
    public function getPermissionById($id)
    {
        validate(['id' => 'integer'])->check(['id' => $id]);
        $permission = Permission::getDataById($id);

        return $permission;
    }

    /**
     * 以ID删除单个权限.
     *
     * @param int $id 权限ID
     *
     * @return app\account\Model\Permission Permission 模型类
     */
    public function deletePermissionById($id)
    {
        validate(['id' => 'integer'])->check(['id' => $id]);

        return Permission::deleteDataById($id);
    }

    /**
     *  以控制器controller和动作action查找权限.
     *
     * @param string $controller 控制��
     * @param string $action     动作
     *
     * @return app\account\Model\Permission Permission 模型类
     */
    public function getPermissionByControllerAndAction($controller, $action)
    {
        $params = [['controller' => $controller], ['action' => $action]];
        $permission = Permission::getDataByField($params);
        if (isset($permission->error)) {
            $this->error = $permission->error;
        }

        return $permission;
    }

    //############################# Auth中间件 #############################

    /**
     * 授权中间件.
     *
     * @param string $username   用户名
     * @param string $controller 控制器
     * @param string $action     动作
     *
     * @return app\account\Model\Permission Permission 模型类
     */
    public function authInMiddleWare($username, $controller, $action)
    {
        $user = User::where(['username' => $username])->findOrEmpty();

        $permission = Permission::getDataByField([['controller' => $controller], ['action' => $action]]);
        if (isset($permission->error)) {
            $this->error = $permission->error;
        }
        if (!$user->isEmpty()) {
            foreach ($user->roles as $role) {
                if (in_array($permission->id, explode(',', $role->permission))) {
                    return true;
                    break;
                }
            }
        }

        return false;
    }

    //############################# Login #############################

    public function confirmUser($params)
    {
        $id = $params['id'];
        $phone = $params['phone'];
        $email = $params['email'];

        if (1 == $id) {
            // code... send SMS
        }

        if (1 == $id) {
            // code... send email link
        }
    }
}
