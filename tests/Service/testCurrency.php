<?php

declare(strict_types=1);

namespace CurrencyApp\Tests\Service;

use CurrencyApp\Provider\BinListNet;
use CurrencyApp\Provider\ExchangeRatesIo;
use CurrencyApp\Service\Currency;
use PHPUnit\Framework\TestCase;

class testCurrency extends TestCase
{
    /**
     * @dataProvider requestProvider
     */
    public function testCalculate(string $requestString, array $responseArray)
    {
        $rateProvider = new ExchangeRatesIo();
        $binProvider  = new BinListNet();
        $service      = new Currency($rateProvider, $binProvider);

        $data = $service->calculate($requestString);
        $this->assertEquals($responseArray, $data);
    }

    public function requestProvider()
    {
        return [
            ['{"bin":"45717360","amount":"100.00","currency":"EUR"}', [1.00]],
            ['{"bin":"516793","amount":"50.00","currency":"USD"}', [0.43]],
            ['{"bin":"45417360","amount":"10000.00","currency":"JPY"}', [1.64]],
            ['{"bin":"41417360","amount":"130.00","currency":"USD"}', [2.22]],
            ['{"bin":"4745030","amount":"2000.00","currency":"GBP"}', [43.98]],
        ];
    }
}
