<?php

namespace IMEdge\Log\Writer;

use Amp\ByteStream\WritableResourceStream;
use gipfl\Log\Logger;
use gipfl\Log\PrefixLogger;
use IMEdge\Log\ProcessLogger;

// Hint: avoid systemd/journald logging. Might be removed / improved
class ProcessStdoutLogger extends ProcessLogger
{
    protected static function applyWriter(Logger &$logger, string $identifier): void
    {
        $logger->addWriter(new AmpStreamWriter(new WritableResourceStream(STDERR)));
        $logger = new PrefixLogger($identifier . ': ', $logger);
    }
}
