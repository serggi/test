<?php

declare(strict_types=1);

namespace CurrencyApp\Service;

use CurrencyApp\Helper\Country;
use CurrencyApp\Provider\BinProviderInterface;
use CurrencyApp\Provider\RateProviderInterface;
use stdClass;

final class Currency implements ConverterInterface
{
    private const BASE_CURRENCY        = 'EUR';
    private const EU_TAX_AMOUNT        = 0.01;
    private const NON_EU_TAX_AMOUNT    = 0.02;

    private $binProvider;
    private $rateProvider;

    public function __construct(RateProviderInterface $rateProvider, BinProviderInterface $binProvider)
    {
        $this->binProvider  = $binProvider;
        $this->rateProvider = $rateProvider;
    }

    public function calculate(string $dataString)
    {
        $result       = [];
        $transactions = explode(PHP_EOL, $dataString);

        foreach ($transactions as $transaction) {
            $data     = json_decode($transaction, true);
            $bin      = $this->getBin($data);
            $amount   = $this->getAmount($data);
            $currency = $this->getCurrency($data);

            if ($bin === null || $amount === null || $currency === null) {
                continue;
            }

            $binResponse = $this->getDataByBin($bin);
            $isEuCountry = $this->isCountryBelongsToEU($binResponse->country->alpha2);
            $subTotal    = $this->getCorrectAmount($currency, $amount);

            $result[] = $this->getAmountWithTax($isEuCountry, $subTotal);
        }

        return $result;
    }

    private function getRates(): array
    {
        return $this->rateProvider->getRates();
    }

    private function getDataByBin(string $bin): StdClass
    {
        return $this->binProvider->getDataByBin($bin);
    }

    private function getCorrectAmount(string $currency, float $amount): float
    {
        $rates = $this->getRates();
        if ($currency !== self::BASE_CURRENCY && $rates[$currency] > 0) {
            return ceil($amount / $rates[$currency]);
        }

        return ceil($amount);
    }

    private function isCountryBelongsToEU(string $code): bool
    {
        return array_key_exists($code, array_flip(Country::EU_COUNTRY_CODE_LIST));
    }

    private function getAmountWithTax(bool $isEuCountry, float $amount): float
    {
        return $isEuCountry ? $amount * self::EU_TAX_AMOUNT : $amount * self::NON_EU_TAX_AMOUNT;
    }

    private function getBin(array $data): ?string
    {
        return isset($data['bin']) && !empty($data['bin'])
            ? $data['bin']
            : null;
    }

    private function getAmount(array $data): ?float
    {
        return isset($data['amount']) && $data['amount'] !== ''
            ? (float)$data['amount']
            : null;
    }

    private function getCurrency(array $data): ?string
    {
        return isset($data['currency']) && !empty($data['currency'])
            ? $data['currency']
            : null;
    }
}
