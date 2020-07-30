<?php

declare(strict_types=1);

namespace CurrencyApp\Provider;

use stdClass;

interface BinProviderInterface
{
    public function getDataByBin(string $bin): StdClass;
}
