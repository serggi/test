<?php

declare(strict_types=1);

namespace CurrencyApp\Entity;

class Transaction
{
    private string $bin;
    private float  $amount;
    private string $currency;
    private bool   $isEU;

    public function getBin(): string
    {
        return $this->bin;
    }

    public function setBin(string $bin): self
    {
        $this->bin = $bin;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function isEU(): bool
    {
        return $this->isEU;
    }

    public function setEU(bool $isEU): self
    {
        $this->isEU = $isEU;

        return $this;
    }
}
