<?php

declare(strict_types=1);

namespace CurrencyApp\Service;

use CurrencyApp\Entity\Transaction;
use CurrencyApp\Provider\BinProviderInterface;
use CurrencyApp\Provider\RateProviderInterface;

final class Commission
{
    private const BASE_CURRENCY        = 'EUR';
    public  const EU_TAX_AMOUNT        = 0.01;
    public  const NON_EU_TAX_AMOUNT    = 0.02;

    private RateProviderInterface $rateProvider;
    private TransactionDataParser $dataParser;

    public function __construct(RateProviderInterface $rateProvider, BinProviderInterface $binProvider)
    {
        $this->rateProvider = $rateProvider;
        $this->dataParser   = new TransactionDataParser($binProvider);
    }
    
    public function init(string $dataString)
    {
        $result       = [];
        $transactions = $this->dataParser->parse($dataString);

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            if ($transaction !== null) {
                $result[] = $this->calculate($transaction);
            }
        }

        return $result;
    }

    public function calculate(Transaction $transaction): float
    {
        $commissionRate = $transaction->isEU()
            ? self::EU_TAX_AMOUNT
            : self::NON_EU_TAX_AMOUNT;

        $amountInEUR = $transaction->getAmount();
        if ($transaction->getCurrency() !== self::BASE_CURRENCY) {
            $exchangeRate = $this->getExchangeRate($transaction->getCurrency());
            if ($exchangeRate > 0) {
                $amountInEUR = $transaction->getAmount() / $exchangeRate;
            }
        }

        return (ceil($amountInEUR * $commissionRate * 100) / 100);
    }

    private function getExchangeRate(string $currencyCode): float
    {
        return $this->rateProvider->getRateByCurrencyCode($currencyCode);
    }
}
