<?php

namespace app;

use think\model;

class BaseModel extends model
{
    /*******************类属性*******************/

    /**
     * @var string 指定自动写入时间戳的类型为dateTime类型
     */
    protected $autoWriteTimestamp = true;

    /**
     * @var string 数据库定义时间戳字段名
     */
    public $createTime = 'create_time';
    public $updateTime = 'update_time';

    /**
     * @var array 定义类型转换
     */
    public $type = [
        'create_time' => 'timestamp:Y/m/d H:i:s',
        'update_time' => 'timestamp:Y/m/d H:i:s',
    ];

    /**
     * 创建时间搜索器.
     *
     * @param object $query
     * @param array  $value 时间数组
     */
    public function searchCreateTimeAttr($query, $value)
    {
        $query->whereBetweenTime('create_time', $value[0], $value[1]);
    }

    /**
     *  以ID获取单个数据.
     *
     * @return app\account\Model\User User 模型类
     */
    public function getDataById($id)
    {
        return  $this->findOrEmpty($id);
    }

    /**
     *  获取多个数据列表.
     *
     * @param array $keywords 字段数组
     * @param int   $page     页码
     * @param int   $limit    每页数量
     *
     * @return array 模型类数据集
     */
    public function getDataCollection($keywords = null, $page = null, $limit = null, $sort = null, $order = null)
    {
        // 搜索字段
        $collection = $this;
        if (!empty($keywords) && is_array($keywords)) {
            $collection = $collection->withSearch(array_keys($keywords), $keywords);
        }

        // 若有分页
        if ($page && $limit) {
            $collection = $collection->page($page, $limit);
        }

        // 若有排序
        if ($sort && $order) {
            $collection = $collection->order($sort, $order);
        }

        return $collection->select();
    }

    /**
     *  增加数据.
     *
     * @param array $params 参数属性
     *
     * @return app\account\Model\User User 模型类
     */
    public function createData($data)
    {
        try {
            $data = $this->create($data);

            return $data;
        } catch (\Throwable $th) {
            $this->error = '错误:'.$th->getMessage();

            return $this;
        }
    }

    /**
     *  以ID更新数据.
     *
     * @param array $params 参数属��
     * @param int   $id     用户ID
     *
     * @return app\account\Model\User User 模型类
     */
    public function updateDataById($data, $id)
    {
        try {
            $object = $this->find($id);
            if ($object) {
                $object->save($data);

                return $object;
            }
            $this->error = '找不到要更新的数据';

            return $this;
        } catch (\Throwable $th) {
            $this->error = '错误:'.$th->getMessage();

            return $this;
        }
    }

    /**
     *  以ID删除数据.
     *
     * @param int $id 用户ID
     *
     * @return bool boolen 布尔值
     */
    public function deleteDataById($id)
    {
        try {
            $this->startTrans();
            $data = $this->findOrEmpty($id);
            $data = $data->delete($id);

            $this->commit();

            return $data;
        } catch (\Throwable $th) {
            $this->rollback();
            $this->error = '错误:'.$th->getMessage();
        }
    }

    public function getDataByField($fields = [])
    {
        $i = 0;
        foreach ($fields as $value) {
            if (0 === $i) {
                $data[0] = $this->where(array_keys($value)[0], array_values($value)[0]);
            }
            ++$i;
            $data[$i] = $data[$i - 1]->where(array_keys($value)[0], array_values($value)[0]);
        }

        return $data[$i]->findOrEmpty();
    }

    public function getDataCollectionByField($fields = [])
    {
        return $this->where($fields)->select();
    }

    public function sumDataByField(Type $var = null)
    {
        // code...
    }
}
