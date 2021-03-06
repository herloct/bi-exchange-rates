<?php
namespace Kuartet\BI\Domain;

use \Carbon\Carbon;
use \PHPUnit_Framework_TestCase;

class RateTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $code = 'IDR';
        $name = 'Indonesian Rupiah';
        $sell = 10000;
        $buy = 11000;
        $middle = 10500;
        $updatedAt = Carbon::now();

        $exchangeRate = new Rate($code, $name, $sell, $buy, $updatedAt);
        $this->assertInstanceOf('Kuartet\BI\Domain\RateInterface', $exchangeRate);
        $this->assertEquals($code, $exchangeRate->getCode());
        $this->assertEquals($name, $exchangeRate->getName());
        $this->assertEquals($sell, $exchangeRate->getSell());
        $this->assertEquals($buy, $exchangeRate->getBuy());
        $this->assertEquals($middle, $exchangeRate->getMiddle());
        $this->assertEquals($updatedAt, $exchangeRate->getUpdatedAt());
    }
}
