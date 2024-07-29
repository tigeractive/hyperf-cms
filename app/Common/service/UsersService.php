<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Common\service;

use App\Common\model\mysql\UsersModel;
use App\helpers\Common;
use Hyperf\DbConnection\Db;

class UsersService extends BaseService
{
    public ?UsersModel $model = null;

    public function __construct()
    {
        $this->model = new UsersModel();
    }

    public function getUserList($request, $params = [])
    {
        // 查询条件的组装
        $where = [];
        if (! empty($params['user_id'])) {
            $where[] = ['user_id', '=', $params['user_id']];
        }
        if (! empty($params['username'])) {
            $where[] = ['username', 'like', '%' . $params['username'] . '%'];
        }
        if (! empty($params['state'])) {
            $where[] = ['state', '=', $params['state']];
        }

        // 隐藏超级管理员
        $where[] = ['user_id', '<>', 1];
        $list = $this->model->getUserList($request, $where);
        $total = $this->model::query()->where($where)->count();
        if (empty($list)) {
            return [];
        }
        $page = [
            'total' => $total,
        ];

        return [
            'list' => $list,
            'page' => $page,
        ];
    }

    public function getUserByName($userName)
    {
        return $this->model::query()->where('username', $userName)->first();
    }

    public function getUserByUserId($userId)
    {
        return $this->model::query()->where('user_id', $userId)->first();
    }

    public function getUserByNameNotId($userId, $username)
    {
        return $this->model::query()->where('user_id', '<>', $userId)
            ->where('username', $username)
            ->first();
    }


    // 登录时间修改，登录ip修改
    public function updateLoginInfo($userId, $ip)
    {
        return $this->model::query()->where('user_id', $userId)
            ->update([
                'login_time' => time(),
                'login_ip' => $ip,
                'update_time' => Db::raw('update_time'), // 禁用update_time自动更新
            ]);
    }

    public function add($data)
    {
        return $this->model::query()->create($data);
    }

    public function edit($data)
    {
        return $this->model->edit($data);
    }

    public function del($userId)
    {
        return $this->model::destroy($userId);
    }
}
