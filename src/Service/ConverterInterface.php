<?php

declare(strict_types=1);

namespace CurrencyApp\Service;

interface ConverterInterface
{
    public function calculate(string $data);
}
