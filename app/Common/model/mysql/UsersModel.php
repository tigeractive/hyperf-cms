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

namespace App\Common\model\mysql;

use App\helpers\Common;

class UsersModel extends BaseModel
{
    protected ?string $table = 'users';

    protected string $primaryKey = 'user_id';

//    protected array $dates = ['create_time', 'update_time', 'login_time'];
//
//    public function getLoginTimeAttribute($value)
//    {
//        return strtotime($value);
//    }

    protected array $fillable = ['username', 'password', 'phone', 'job', 'state', 'role_list', 'lang'];

    public function getRoleListAttribute($value)
    {
        return ! empty($value) ? array_map('intval', explode(',', $value)) : '';
    }

    // 处理角色列表字段
    public function setRoleListAttribute($value)
    {
        if (!empty($value) && is_array($value)) {
            $this->attributes['role_list'] = implode(',', $value);
        } else {
            $this->attributes['role_list'] = '';
        }
    }

    public function getUserList($request, $where = '')
    {
        [$start, $pageSize] = Common::paginate($request);
        return self::query()->where($where)
            ->offset($start)
            ->limit($pageSize)
            ->get();
    }

    public function edit($data)
    {
        $user = self::query()->find($data['user_id']);
        if (! empty($data['password'])) {
            $data['password'] = Common::packagePassword($data['password']);
            $user->password = $data['password'];
        }

        $user->username = $data['username'];
        $user->phone = $data['phone'] ?? '';
        $user->job = $data['job'] ?? '';
        $user->state = $data['state'] ?? 1;
        $user->role_list = $data['role_list'] ?? '';

        return $user->save();
    }
}
