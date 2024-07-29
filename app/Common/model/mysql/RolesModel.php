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

class RolesModel extends BaseModel
{
    protected ?string $table = 'roles';

    protected string $primaryKey = 'role_id';

    protected array $fillable = [
        'role_name',
        'remark'
    ];

    public function getMenuIdListAttribute($value)
    {
        return array_filter(explode(',', $value));
    }

    public function getFloorCheckedKeysAttribute($value)
    {
        if (! empty($value)) {
            return array_filter(explode(',', $value));
        }
    }

    public function getHasChildKeysAttribute($value)
    {
        if (! empty($value)) {
            return array_filter(explode(',', $value));
        }
    }

    // 获取角色列表
    public function getRoleList($request, $where)
    {
        [$start, $pageSize] = Common::paginate($request);

        return self::query()->where($where)
            ->offset($start)
            ->limit($pageSize)
            ->orderBy('create_time', 'desc')
            ->get();
    }
}
