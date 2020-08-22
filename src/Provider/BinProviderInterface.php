<?php

declare(strict_types=1);

namespace CurrencyApp\Provider;

interface BinProviderInterface
{
    public function getCountryCodeByBin(string $bin): ?string;
}
