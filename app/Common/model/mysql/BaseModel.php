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

use App\Model\Model;
use Carbon\Carbon;
use Nette\Utils\DateTime;

class BaseModel extends Model
{
    public const CREATED_AT = 'create_time';

    public const UPDATED_AT = 'update_time';

    public bool $timestamps = true;

    // 访问器：当设置属性时转换日期到时间戳
    public function setCreateTimeAttribute($value)
    {
        // 如果传入的是日期实例或者可以解析为日期的字符串
        if ($value instanceof DateTime || ($value = Carbon::parse($value)->getTimestamp()) !== false) {
            // 将日期转换为时间戳
            $this->attributes['create_time'] = $value;
        }
    }

    public function setUpdateTimeAttribute($value)
    {
        // 如果传入的是日期实例或者可以解析为日期的字符串
        if ($value instanceof DateTime || ($value = Carbon::parse($value)->getTimestamp()) !== false) {
            // 将日期转换为时间戳
            $this->attributes['update_time'] = $value;
        }
    }
}
