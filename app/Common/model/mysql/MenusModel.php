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

class MenusModel extends BaseModel
{
    protected ?string $table = 'menus';

    protected string $primaryKey = 'menu_id';

    protected array $fillable = [
        'menu_name',
        'menu_type',
        'menu_multilang',
        'icon',
        'path',
        'component',
        'url',
        'menu_code',
        'menu_state',
        'parent_id_list',
        'parent_id',
        'sort_id',
    ];

    public function setSortIdAttribute($value)
    {
        $this->attributes['sort_id'] = is_string($value) && empty(trim($value)) ? 0 : $value;
    }

    public function getMenuList($where)
    {
        return self::query()->where($where)
            ->orderBy('sort_id', 'DESC')
            ->get();
    }
}
