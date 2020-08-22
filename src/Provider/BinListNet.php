<?php

declare(strict_types=1);

namespace CurrencyApp\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class BinListNet implements BinProviderInterface
{
    private const PROVIDER_URL = 'https://lookup.binlist.net/';
    private Client $client;
    private ?string $apiKey;

    public function __construct(string $apiKey = null, Client $client = null)
    {
        $this->client = $client ?? new Client();
        $this->apiKey = $apiKey;
    }

    public function getCountryCodeByBin(string $bin): ?string
    {
        try {
            $response = $this->client
                ->get($bin, [
                    'base_uri' => self::PROVIDER_URL,
                ])
                ->getBody()
                ->getContents();
        } catch (BadResponseException $exception) {
            // log $exception->getMessage();
            return null;
        }

        return (!empty($response))
            ? json_decode($response, true)['country']['alpha2']
            : null;
    }
}
