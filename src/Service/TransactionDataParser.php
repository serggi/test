<?php

declare(strict_types=1);

namespace CurrencyApp\Service;

use CurrencyApp\Entity\Transaction;
use CurrencyApp\Exception\JSONException;
use CurrencyApp\Helper\Country;
use CurrencyApp\Provider\BinProviderInterface;

class TransactionDataParser
{
    private BinProviderInterface $binProvider;

    public function __construct(BinProviderInterface $binProvider)
    {
        $this->binProvider = $binProvider;
    }

    public function parse(string $dataString): array
    {
        $result       = [];
        $transactions = explode(PHP_EOL, $dataString);
        foreach ($transactions as $transaction) {
            try {
                $result[] = $this->buildTransactionFromRequest(json_decode($transaction, true));
            } catch (JSONException $e) {
                // TODO LOGGING
                continue;
            }
        }

        return $result;
    }

    public function buildTransactionFromRequest(array $data): ?Transaction
    {
        if (empty($data['bin'])
            || empty($data['amount'])
            || empty($data['currency'])
        ) {
            // log
            return null;
        }

        $transaction = new Transaction();

        return $transaction
            ->setBin($data['bin'])
            ->setAmount((float) $data['amount'])
            ->setCurrency($data['currency'])
            ->setEU($this->isCountryBelongsToEU($this->getCountryCodeByBin($transaction->getBin())));
    }

    private function isCountryBelongsToEU(string $code): bool
    {
        return array_key_exists($code, array_flip(Country::EU_COUNTRY_CODE_LIST));
    }

    private function getCountryCodeByBin(string $bin): ?string
    {
        return $this->binProvider->getCountryCodeByBin($bin);
    }
}
