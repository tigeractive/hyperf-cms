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

use Hyperf\HttpMessage\Stream\SwooleStream;

class ReturnData
{
    public static function getInstance()
    {
        return new static();
    }

    public function show($response, $codeResponse, $msg = '', $data = null)
    {
        $result = ['msg' => $msg, 'code' => $codeResponse['code']];
        if (! is_null($data)) {
            if (is_array($data)) {
                $data = array_filter($data, function ($item) {
                    return $item != null;
                });
            }
            $result['data'] = $data;
        }
        return $response->setStatus($codeResponse['httpCode'] ?? 200)->setHeaders(['Content-Type' => 'application/json'])->setBody(new SwooleStream(json_encode($result)));
    }

    public static function trimArr($data)
    {
        return array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);
    }
}
