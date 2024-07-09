<?php

namespace IMEdge\Log;

use Amp\ByteStream\WritableResourceStream;
use GetOpt\GetOpt;
use gipfl\Log\Logger;
use gipfl\Log\PrefixLogger;

// Hint: avoid systemd/journald logging. Might be removed / improved
class ProcessStdoutLogger extends ProcessLogger
{
    public static function detectAndApplyLogWriter(
        Logger &$logger,
        ?string $identifier = null,
        ?GetOpt $options = null
    ): void {
        $logger->addWriter(new AmpStreamWriter(new WritableResourceStream(STDERR)));
        $logger = new PrefixLogger($identifier . ': ', $logger);
    }
}
