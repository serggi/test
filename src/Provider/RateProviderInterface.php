<?php

declare(strict_types=1);

namespace CurrencyApp\Provider;

interface RateProviderInterface
{
    public function getRates(): array;
}
