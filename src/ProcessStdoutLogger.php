<?php

namespace IMEdge\Log;

use Amp\ByteStream\WritableResourceStream;
use GetOpt\GetOpt;
use gipfl\Log\Filter\LogLevelFilter;
use gipfl\Log\Logger;

class ProcessStdoutLogger
{
    public static function createForOptions(GetOpt $options): Logger
    {
        $logger = new Logger();
        self::applyLogFilters($logger, $options);

        return $logger;
    }

    public static function detectAndApplyLogWriter(Logger $logger): void
    {
        $logger->addWriter(new AmpStreamWriter(new WritableResourceStream(STDERR)));
    }

    protected static function applyLogFilters(Logger $logger, GetOpt $options)
    {
        if (! $options->getOption('debug')) {
            if ($options->getOption('verbose')) {
                $logger->addFilter(new LogLevelFilter('info'));
            } else {
                $logger->addFilter(new LogLevelFilter('notice'));
            }
        }
    }
}
