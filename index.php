<?php

declare(strict_types=1);

use CurrencyApp\Provider\BinListNet;
use CurrencyApp\Provider\ExchangeRatesIo;
use CurrencyApp\Service\Currency;

require_once 'vendor/autoload.php';

$fileToRead = file_get_contents($argv[1]);
$service    = new Currency(new ExchangeRatesIo(), new BinListNet());
$result     = $service->calculate($fileToRead);

foreach ($result as $item) {
    echo $item . PHP_EOL;
}
