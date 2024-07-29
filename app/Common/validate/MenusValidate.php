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

class MenusValidate extends BaseValidate
{
    protected $rule = [
        'menu_name' => 'required',
        'menu_id' => 'required|checkchild',
        'sort_id' => 'numeric',
    ];

    protected $message = [
        'menu_name.required' => '菜单名称不能为空',
        'menu_id.required' => '菜单id不能为空',
        'menu_id.checkchild' => '该菜单下面有子类，请先删除子类',
        'sort_id.numeric' => '排序数字必须为整数',
    ];

    protected $scene = [
        'add' => 'menu_name.required|sort_id.numeric',
        'edit' => 'menu_id.required|menu_name.required|sort_id.numeric',
        'del' => 'menu_id.required|menu_id.checkchild',
    ];
}
