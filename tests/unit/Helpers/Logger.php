<?php

namespace Test\Helpers;

use Monolog\Logger as BaseLogger;
use DesInventar\Helpers\LoggerHelper;

class Logger
{
    public static function logger()
    {
        return LoggerHelper::logger(['file' => '/dev/null', 'level' => BaseLogger::DEBUG]);
    }
}
