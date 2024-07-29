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

namespace App\Exception;

use Exception;

class BaseException extends Exception
{
    // HTTP 状态码
    public $httpCode = 400;

    // 错误具体信息
    public $msg = '参数错误';

    // 自定义的错误码
    public $code = 400;

    public function __construct($params = [])
    {
        if (! is_array($params)) {
            return;
        }

        if (array_key_exists('httpCode', $params)) {
            $this->httpCode = $params['httpCode'];
        }

        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }

        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }
    }
}
