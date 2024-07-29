<?php

namespace App\Exception;

class PrivilegeException extends BaseException
{
    public $httpCode = 200;

    public $msg = '没有此权限';

    public $code = 500002;
}