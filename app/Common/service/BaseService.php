<?php

namespace App\Common\service;

class BaseService
{
    public static function getInstance()
    {
        return new static();
    }
}