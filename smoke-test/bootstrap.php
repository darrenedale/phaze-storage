<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

function fatal(string $message, int $code = 1): void
{
    fprintf(STDERR, "[FATAL] (%d) %s\n", $code, $message);
    die($code);
}

(function () use ($argv)
{
    $test = basename($argv[0], ".php");
    echo "Phaze storage smoke test \"{$test}\"\n";
    echo "PHP v" . PHP_VERSION . " [" . PHP_VERSION_ID . "]\n\n";
})();
