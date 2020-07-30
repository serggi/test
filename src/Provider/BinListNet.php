<?php

declare(strict_types=1);

namespace CurrencyApp\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class BinListNet implements BinProviderInterface
{
    private const PROVIDER_URL = 'https://lookup.binlist.net/';
    private $client;
    private $apiKey;

    public function __construct(string $apiKey = null, Client $client = null)
    {
        $this->client = $client ?? new Client();
        $this->apiKey = $apiKey;
    }

    public function getDataByBin(string $bin): StdClass
    {
        try {
            $data = $this->client
                ->get($bin, [
                    'base_uri' => self::PROVIDER_URL,
                ])
                ->getBody()
                ->getContents();
        } catch (GuzzleException $exception) {
            echo $exception->getMessage();
        }

        return ($data !== null)
            ? json_decode($data)
            : new StdClass();
    }
}
