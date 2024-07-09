<?php

namespace IMEdge\Log;

use Amp\ByteStream\WritableResourceStream;
use GetOpt\GetOpt;
use gipfl\Log\Filter\LogLevelFilter;
use gipfl\Log\Logger;
use gipfl\Log\Writer\JournaldLogger;
use IMEdge\systemd\systemd;

class ProcessLogger
{
    public static function createForOptions(GetOpt $options): Logger
    {
        $logger = new Logger();
        self::applyLogFilters($logger, $options);

        return $logger;
    }

    public static function detectAndApplyLogWriter(Logger $logger, $identifier, ?GetOpt $options = null): void
    {
        $command = $options?->getCommand();
        // TODO: JsonRpcConnectionWriter if instanceof RpcCommandInterface?
        if (systemd::startedThisProcess()) {
            if (@file_exists(JournaldLogger::JOURNALD_SOCKET)) {
                $logger->addWriter((new JournaldLogger())->setIdentifier($identifier));
            } else {
                $logger->addWriter(new SystemdStdoutWriter());
            }
        } else {
            $logger->addWriter(new AmpStreamWriter(new WritableResourceStream(STDERR)));
        }
    }

    protected static function applyLogFilters(Logger $logger, GetOpt $options): void
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
