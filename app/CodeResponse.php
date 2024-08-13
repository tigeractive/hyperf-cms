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
    const LANG = 'lang';

    // 通用返回码
    public const SUCCESS = ['code' => 200, 'httpCode' => 200];

    public const FAIL = ['code' => 402, 'httpCode' => 200];

    public const TOKENFAIL = ['code' => 500001, 'httpCode' => 401];

    public const LOGINERROR = ['code' => 401];

    // 默认页数
    public const PAGESIZE = 10;

    // 密码盐
    public const PWDSALT = 'southtiger112';

    // 操作标识
    public const ADD = 'add';

    public const EDIT = 'edit';

    // 用户添加
    public const USERADDSUCCESS = ['code' => 200, 'httpCode' => 200];

    public const USERADDFAIL = ['code' => 200, 'httpCode' => 200];

    // 用户编辑
    public const USEREDITSUCCESS = ['code' => 200, 'httpCode' => 200];

    public const USEREDITFAIL = ['code' => 200, 'httpCode' => 200];

    // 用户删除
    public const USERDELSUCCESS = ['code' => 200, 'httpCode' => 200];

    public const USERDELFAIL = ['code' => 200, 'httpCode' => 200];

    // 角色
    public const ROLESADDSUCCESS = ['code' => 200, 'httpCode' => 200];

    public const ROLESADDFAIL = ['code' => 200, 'httpCode' => 200];

    public const ROLESEDITSUCCESS = ['code' => 200, 'httpCode' => 200];

    public const ROLESEDITFAIL = ['code' => 200, 'httpCode' => 200];

    public const ROLESPERMISSIONSUCCESS = ['code' => 200, 'httpCode' => 200];

    public const ROLESPERMISSIONFAIL = ['code' => 200, 'httpCode' => 200];

    public const ROLESDELETESUCCESS = ['code' => 200, 'httpCode' => 200];

    public const ROLESSUPERDELETEFAIL = ['code' => 200, 'httpCode' => 200];

    public const ROLESDELETEFAIL = ['code' => 200, 'httpCode' => 200];

    // 菜单
    public const MENUSADDSUCCESS = ['code' => 200, 'httpCode' => 200];

    public const MENUSADDFAIL = ['code' => 200, 'httpCode' => 200];

    public const MENUSEDITSUCCESS = [ 'code' => 200, 'httpCode' => 200];

    public const MENUSEDITFAIL = ['code' => 200, 'httpCode' => 200];

    public const MENUSDELSUCCESS = ['code' => 200, 'httpCode' => 200];

    public const MENUSDELFAIL = ['code' => 200, 'httpCode' => 200];
}
