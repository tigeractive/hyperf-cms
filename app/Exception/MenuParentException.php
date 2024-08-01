<?php

namespace App\Exception;

class
MenuParentException extends BaseException
{
    public $httpCode = 200;
    public $msg = '父类不能是本身以及下级类';
    public $code = 400;
}