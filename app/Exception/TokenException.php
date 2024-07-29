<?php

namespace App\Exception;

class TokenException extends BaseException
{
    public $httpCode = 200;

    public $msg = '请重新登录';

    public $code = 500001;
}