<?php

declare(strict_types=1);

namespace CurrencyApp\Provider;

use CurrencyApp\Exception\BadResponseException;
use GuzzleHttp\Client;

class ExchangeRatesIo implements RateProviderInterface
{
    private const PROVIDER_URL = 'https://api.exchangeratesapi.io';
    private Client $client;
    private ?string $apiKey;

    public function __construct(?string $apiKey = null, Client $client = null)
    {
        $this->client = $client ?? new Client();
        $this->apiKey = $apiKey;
    }

    public function getRateByCurrencyCode(string $currencyCode): ?float
    {
        try {
            $data = $this->client
                ->get('latest', [
                    'query' => ['app_id' => $this->apiKey],
                    'base_uri' => self::PROVIDER_URL,
                ])
                ->getBody()
                ->getContents();
        } catch (BadResponseException $exception) {
            // TODO LOGGING
            // echo $exception->getMessage();
        }

        return ($data !== null)
            ? json_decode($data, true)['rates'][$currencyCode]
            : $data;
    }
}
