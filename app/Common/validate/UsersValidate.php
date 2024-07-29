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

namespace App\Common\validate;

class UsersValidate extends BaseValidate
{
    protected $rule = [
        'username' => 'required|unique:users',
        'password' => 'required',
        'user_id' => 'required'
    ];

    protected $message = [
        'username.required' => '用户名称不能为空！',
        'username.unique' => '用户名称已存在！',
        'password.required' => '密码不能为空！',
        'username.updateunique' => '用户名称已存在！',
        'user_id.required' => '用户的ID必须存在'
    ];

    protected $scene = [
        'login' => 'username.required|password.required',
        'add' => 'username.required|username.unique:users|password.required',
        'edit' => 'username.updateunique|username.required',
        'del' => 'user_id.required'
    ];
}
