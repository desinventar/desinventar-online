<?php

namespace Test\Helpers;

use Monolog\Logger as BaseLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    public static function logger()
    {
        $logger = new BaseLogger('test');
        return $logger;
    }
}
