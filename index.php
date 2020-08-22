<?php

declare(strict_types=1);

use CurrencyApp\Provider\BinListNet;
use CurrencyApp\Provider\ExchangeRatesIo;
use CurrencyApp\Service\Commission;

require_once 'vendor/autoload.php';

try {
    $fileToRead = file_get_contents($argv[1]);
    $service    = new Commission(new ExchangeRatesIo(), new BinListNet());
    $result     = $service->init($fileToRead);

    foreach ($result as $item) {
        echo $item . PHP_EOL;
    }
} catch (Exception $exception) {
    // TODO LOGGING OR OTHER LOGIC
}
