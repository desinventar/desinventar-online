<?php

namespace Test\Helpers;

use Monolog\Logger as BaseLogger;
use DesInventar\Helpers\LoggerHelper;

class Logger
{
    public static function logger($output = '/dev/null')
    {
        return LoggerHelper::logger(['file' => $output, 'level' => BaseLogger::DEBUG]);
    }
}
