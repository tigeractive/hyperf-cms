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

namespace App\helpers;

use App\CodeResponse;
use App\Exception\ParameterException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

class Common
{
    //    #[Inject]
    //    private RequestInterface $request;

    //    public static function getInstance()
    //    {
    //        return new static();
    //    }

    public static function paginate($request)
    {
        $pageSize = intval(! empty($request->input('page_size')) ? $request->input('page_size') :
            CodeResponse::PAGESIZE);
        $pageNum = intval(! empty($request->input('page_num')) ? $request->input('page_num') : 1);

        $start = ($pageNum - 1) * $pageSize;

        if ($start < 0 || $pageSize < 0) {
            throw new ParameterException();
        }

        return [$start, $pageSize];
    }

    public static function packagePassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 10]);
    }

    // 通过递归组织数据
    public static function unlimitedForLayer($data, $childName = 'children', $idName = 'id', $pid = 0, $level = 0): array
    {
        $arr = [];
        foreach ($data as $k => $v) {
            if ($v['parent_id'] == $pid) {
                $v['level'] = $level;
                $v[$childName] = self::unlimitedForLayer($data, $childName, $idName, $v[$idName], $level + 1);
                $arr[] = $v;
            }
        }
        return $arr;
    }

    public static function snakeToCamelKeys($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            // 将键名从蛇形转换为驼峰
            $camelKey = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', (string) $key))));

            // 如果值是数组，递归转换
            if (is_array($value)) {
                $value = self::snakeToCamelKeys($value);
            }

            // 设置新的驼峰命名键和相应的值
            $result[$camelKey] = $value;
        }

        return $result;
    }

    function filterArr($data)
    {
        $data = array_filter($data, function ($value) {
            if (is_string($value)) {
                return ! is_null(trim($value)) && trim($value) !== '';
            }
            return $value;
        });
        return array_map('trim', $data);
    }

    public static function trimArr($data)
    {
        return array_map(function($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);
    }
}
