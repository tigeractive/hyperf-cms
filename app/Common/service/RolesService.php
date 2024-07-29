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

use App\Common\model\mysql\RolesModel;
use App\helpers\Common;
use Hyperf\DbConnection\Db;

class RolesService extends BaseService
{
    protected RolesModel $model;

    public function __construct()
    {
        $this->model = new RolesModel();
    }

    public function getRoleListByRoleIds($roleIds)
    {
        return $this->model::query()->whereIn('role_id', $roleIds)->get();
    }

    public function getRoleList($request, $data)
    {
        // 查询条件组装
        $where = [];
        if (! empty($data['role_name'])) {
            $where[] = ['role_name', 'like', '%' . $data['role_name'] . '%'];
        }
        $list = $this->model->getRoleList($request, $where);
        $total = $this->model::query()->where($where)->count();
        $page = [
            'total' => $total,
        ];

        if (! $list) {
            return [];
        }

        return [
            'list' => $list->toArray(),
            'page' => $page,
        ];
    }

    // 获取所有角色
    public function getAllRoles()
    {
        return $this->model::query()->get();
    }

    // 替换menu_id_list、floor_checked_keys、has_child_keys中的菜单id(因为删除菜单，需要去掉相应的菜单id)
    public function updateMenuIdList($menuId)
    {
        $menuIdStr = "$menuId" .  ',';
        $sql = "update tg_roles set menu_id_list=replace(menu_id_list," . "'" . $menuIdStr . "'" . ",'')" . ",floor_checked_keys=replace(floor_checked_keys," . "'" . $menuIdStr . "'" . ",'')" . ",has_child_keys=replace(has_child_keys," . "'" . $menuIdStr . "'" . ",'')";
        Db::statement($sql);
    }

    // 添加角色
    public function add($data)
    {
        return $this->model::query()->create($data);
    }

    public function edit($data)
    {
        $role = $this->model::query()->find($data['role_id']);
        $role->role_name = $data['role_name'];
        $role->remark = $data['remark'] ?? '';
        return $role->save();
    }

    public function del($roleId)
    {
        return $this->model::destroy($roleId);
    }

    public function getRoleByNameNotId($roleId, $roleName)
    {
        return $this->model::query()->where('role_id', '<>', $roleId)
            ->where('role_name', $roleName)
            ->first();
    }

    // 更新权限
    public function updatePermission($data)
    {
        if (empty($data['menu_id_list'])) {
            $data['menu_id_list'] = '';
        } elseif (is_array($data['menu_id_list'])) {
            $data['menu_id_list'] = implode(',', $data['menu_id_list']) . ',';
        }

        if (empty($data['floor_checked_keys'])) {
            $data['floor_checked_keys'] = '';
        } elseif (is_array($data['floor_checked_keys'])) {
            $data['floor_checked_keys'] = implode(',', $data['floor_checked_keys']) . ',';
        }

        if (empty($data['has_child_keys'])) {
            $data['has_child_keys'] = '';
        } elseif (is_array($data['has_child_keys'])) {
            $data['has_child_keys'] = implode(',', $data['has_child_keys']) . ',';
        }

        $role = $this->model::query()->find($data['role_id']);
        $role->menu_id_list = $data['menu_id_list'];
        $role->floor_checked_keys = $data['floor_checked_keys'];
        $role->has_child_keys = $data['has_child_keys'];

        return $role->save();
    }
}
