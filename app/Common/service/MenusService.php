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

use App\Common\model\mysql\MenusModel;
use App\Exception\MenuParentException;
use App\helpers\Common;

class MenusService extends BaseService
{
    protected MenusModel $model;

    public function __construct()
    {
        $this->model = new MenusModel();
    }

    public function getMenuListByMenuIds($menuIds)
    {
        return $this->model::query()->whereIn('menu_id', $menuIds)
            ->orderBy('sort_id', 'desc')
            ->get();
    }

    public function getMenuList($data)
    {
        $where = [];
        if (! empty($data['menu_name'])) {
            $where[] = ['menu_name', 'like', '%' . $data['menu_name'] . '%'];
        }
        if (! empty($data['menu_state'])) {
            $where[] = ['menu_state', '=', $data['menu_state']];
        }
        $list = $this->model->getMenuList($where)->toArray();
        if (empty($list)) {
            return [];
        }

        $list = array_map(function ($v) {
            if (! empty($v['parent_id_list'])) {
                $v['parent_id_list'] = explode(',', $v['parent_id_list']);
                $v['parent_id_list'] = array_map(function ($v) {
                    return intval($v);
                }, $v['parent_id_list']);
            }
            return $v;
        }, $list);

        if (! empty($data['menu_name'])) {
            return $list;
        }

        if (! empty($data['menu_state']) && $data['menu_state'] == 2) {
            return $list;
        }

        // 将组织的数据进行处理，子类在父类下面
        return Common::unlimitedForLayer($list, 'children', 'menu_id');
    }

    public function getAllMenuList()
    {
        $list = $this->model->getMenuList('')->toArray();
        // 将组织的数据进行处理，子类在父类下面
        return Common::unlimitedForLayer($list, 'children', 'menu_id');
    }

    public function add($data)
    {
        if (! empty($data['parent_id_list'])) {
            $parentIdArr = $data['parent_id_list'];
            $data['parent_id'] = $parentIdArr[count($parentIdArr) - 1];
            $data['parent_id_list'] = implode(',', $data['parent_id_list']);
        } else {
            $data['parent_id_list'] = '';
        }

        return $this->model::query()->create($data);
    }

    public function edit($data)
    {
        if (! empty($data['parent_id_list'])) {
            $parentIdArr = $data['parent_id_list'];
            if (in_array($data['menu_id'], $parentIdArr)) {
                throw new MenuParentException();
            }
            $data['parent_id'] = $parentIdArr[count($parentIdArr) - 1];
            $data['parent_id_list'] = implode(',', $data['parent_id_list']);
        } else {
            $data['parent_id'] = 0;
        }
        $menu = $this->model::query()->find($data['menu_id']);
        $menu->menu_type = $data['menu_type'] ?? 1;
        $menu->menu_name = $data['menu_name'] ?? '';
        $menu->menu_multilang = $data['menu_multilang'] ?? '';
        $menu->icon = $data['icon'] ?? '';
        $menu->path = $data['path'] ?? '';
        $menu->component = $data['component'] ?? '';
        $menu->url = $data['url'] ?? '';
        $menu->menu_code = $data['menu_code'] ?? '';
        $menu->menu_state = $data['menu_state'] ?? 1;
        $menu->parent_id_list = $data['parent_id_list'] ?? '';
        $menu->parent_id = $data['parent_id'] ?? 0;
        $menu->sort_id = $data['sort_id'] ?? 0;

        return $menu->save();
    }

    public function del($menuId)
    {
        return $this->model::destroy($menuId);
    }

    public function isExistChildByParentId($parentId)
    {
        $result = $this->model::query()->where('parent_id', $parentId)->first();
        if ($result) {
            return true;
        }
        return false;
    }
}
