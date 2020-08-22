<?php

declare(strict_types=1);

namespace CurrencyApp\Service;

interface CalculationInterface
{
    public function calculate(string $data);
}
