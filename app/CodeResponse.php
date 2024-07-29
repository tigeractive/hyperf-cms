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

namespace App;

class CodeResponse
{
    // 通用返回码
    public const SUCCESS = ['msg' => '', 'code' => 200, 'httpCode' => 200];

    public const FAIL = ['msg' => '', 'code' => 402, 'httpCode' => 200];

    public const TOKENFAIL = ['msg' => 'token非法', 'code' => 500001, 'httpCode' => 401];

    public const LOGINERROR = ['msg' => '用户名或者密码不正确！', 'code' => 401];

    // 默认页数
    public const PAGESIZE = 10;

    // 密码盐
    public const PWDSALT = 'southtiger112';

    // 操作标识
    public const ADD = 'add';

    public const EDIT = 'edit';

    // 用户添加
    public const USERADDSUCCESS = ['msg' => '用户添加成功', 'code' => 200, 'httpCode' => 200];

    public const USERADDFAIL = ['msg' => '用户添加失败', 'code' => 200, 'httpCode' => 200];

    // 用户编辑
    public const USEREDITSUCCESS = ['msg' => '用户更新成功', 'code' => 200, 'httpCode' => 200];

    public const USEREDITFAIL = ['msg' => '用户更新失败', 'code' => 200, 'httpCode' => 200];

    // 用户删除
    public const USERDELSUCCESS = ['msg' => '用户删除成功', 'code' => 200, 'httpCode' => 200];

    public const USERDELFAIL = ['msg' => '用户删除失败', 'code' => 200, 'httpCode' => 200];

    // 角色
    public const ROLESADDSUCCESS = ['msg' => '角色添加成功', 'code' => 200, 'httpCode' => 200];

    public const ROLESADDFAIL = ['msg' => '角色添加失败', 'code' => 200, 'httpCode' => 200];

    public const ROLESEDITSUCCESS = ['msg' => '角色编辑成功', 'code' => 200, 'httpCode' => 200];

    public const ROLESEDITFAIL = ['msg' => '角色编辑失败', 'code' => 200, 'httpCode' => 200];

    public const ROLESPERMISSIONSUCCESS = ['msg' => '角色编辑成功', 'code' => 200, 'httpCode' => 200];

    public const ROLESPERMISSIONFAIL = ['msg' => '角色编辑失败', 'code' => 200, 'httpCode' => 200];

    public const ROLESDELETESUCCESS = ['msg' => '角色删除成功', 'code' => 200, 'httpCode' => 200];

    public const ROLESSUPERDELETEFAIL = ['msg' => '超级管理员角色不能删除', 'code' => 200, 'httpCode' => 200];

    public const ROLESDELETEFAIL = ['msg' => '角色删除失败', 'code' => 200, 'httpCode' => 200];

    // 菜单
    public const MENUSADDSUCCESS = ['msg' => '菜单添加成功', 'code' => 200, 'httpCode' => 200];

    public const MENUSADDFAIL = ['msg' => '菜单添加失败', 'code' => 200, 'httpCode' => 200];

    public const MENUSEDITSUCCESS = ['msg' => '菜单更新成功', 'code' => 200, 'httpCode' => 200];

    public const MENUSEDITFAIL = ['msg' => '菜单更新失败', 'code' => 200, 'httpCode' => 200];

    public const MENUSDELSUCCESS = ['msg' => '菜单删除成功', 'code' => 200, 'httpCode' => 200];

    public const MENUSDELFAIL = ['msg' => '菜单删除失败', 'code' => 200, 'httpCode' => 200];
}
