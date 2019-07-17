<?php

namespace DesInventar\Helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ErrorLogHandler;

class LoggerHelper
{
    public static function logger($config)
    {
        $config = array_merge([
            'level' => getenv('DESINVENTAR_LOGGER_LEVEL') ? getenv('DESINVENTAR_LOGGER_LEVEL') : Logger::DEBUG,
            'file' => getenv('DESINVENTAR_LOGGER_FILE') ? getenv('DESINVENTAR_LOGGER_FILE') : 'php://stdout'
        ], $config);
        $logger = new Logger('logger');
        if (isset($config['file']) && $config['file'] !== '') {
            $logger->pushHandler(new StreamHandler($config['file'], $config['level']));
        }
        if (getenv('DESINVENTAR_FLAGS_ENV') !== 'test') {
            $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::WARNING));
        }
        return $logger;
    }
}
