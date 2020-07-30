<?php

declare(strict_types=1);

namespace CurrencyApp\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ExchangeRatesIo implements RateProviderInterface
{
    private const PROVIDER_URL = 'https://api.exchangeratesapi.io';
    private $client;
    private $apiKey;

    public function __construct(?string $apiKey = null, Client $client = null)
    {
        $this->client = $client ?? new Client();
        $this->apiKey = $apiKey;
    }

    public function getRates(): array
    {
        try {
            $data = $this->client
                ->get('latest', [
                    'query' => ['app_id' => $this->apiKey],
                    'base_uri' => self::PROVIDER_URL,
                ])
                ->getBody()
                ->getContents();
        } catch (GuzzleException $exception) {
            echo $exception->getMessage();
        }

        return ($data !== null)
            ? json_decode($data, true)['rates']
            : [];
    }
}
