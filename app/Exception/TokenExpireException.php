<?php

namespace App\Exception;

class TokenExpireException extends BaseException
{
    public $httpCode = 200;

    public $msg = 'token过期，请重新登录！';

    public $code = 500001;
}