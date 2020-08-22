<?php

declare(strict_types=1);

namespace CurrencyApp\Tests\Service;

use CurrencyApp\Entity\Transaction;
use CurrencyApp\Provider\BinListNet;
use CurrencyApp\Provider\BinProviderInterface;
use CurrencyApp\Provider\ExchangeRatesIo;
use CurrencyApp\Provider\RateProviderInterface;
use CurrencyApp\Service\Commission;
use CurrencyApp\Service\TransactionDataParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class testCommission extends TestCase
{
    /** @var BinProviderInterface | MockObject */
    private $binProvider;
    /** @var RateProviderInterface | MockObject */
    private $rateProvider;
    /** @var Commission | MockObject */
    private $commissionService;
    /** @var TransactionDataParser | MockObject */
    private $transactionDataParser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->binProvider  = $this->getMockBuilder(BinListNet::class)
            ->onlyMethods(['getCountryCodeByBin'])
            ->getMock();
        $this->rateProvider = $this->getMockBuilder(ExchangeRatesIo::class)
            ->onlyMethods(['getRateByCurrencyCode'])
            ->getMock();
        $this->transactionDataParser = new TransactionDataParser($this->binProvider);
        $this->commissionService     = new Commission($this->rateProvider, $this->binProvider);
    }

    protected function tearDown(): void
    {
        $this->binProvider       = null;
        $this->rateProvider      = null;
        $this->commissionService = null;
    }

    public function testCalculationToEU()
    {
        $transaction = new Transaction();
        $transaction
            ->setBin('45717360')
            ->setAmount(100.00)
            ->setCurrency('EUR')
            ->setEU(true);

        $this->rateProvider->expects($this->never())
            ->method('getRateByCurrencyCode');

        $expected = ceil($transaction->getAmount() * Commission::EU_TAX_AMOUNT * 100) / 100;

        $this->assertEquals($expected, $this->commissionService->calculate($transaction));
    }

    public function testCalculationToNonEU()
    {
        $transaction = new Transaction();
        $transaction
            ->setBin('45417360')
            ->setAmount(10000.00)
            ->setCurrency('JPY')
            ->setEU(false);

        $this->rateProvider->expects($this->once())
            ->method('getRateByCurrencyCode')
            ->willReturn(125.74);

        $expected = ceil($transaction->getAmount() / 125.74 * Commission::NON_EU_TAX_AMOUNT * 100) / 100;

        $this->assertEquals($expected, $this->commissionService->calculate($transaction));
    }
}
