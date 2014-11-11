<?php
namespace Kuartet\BI\ExchangeRate;

use \GuzzleHttp\Client;
use \GuzzleHttp\Message\Response;
use \GuzzleHttp\Stream\Stream;
use \GuzzleHttp\Subscriber\Mock;
use \PHPUnit_Framework_TestCase;

class ExchangeRateFinderTest extends PHPUnit_Framework_TestCase
{
    public function testFindAll()
    {
        $responseBody = file_get_contents(__DIR__.'/resources/finder_findAll.html');

        $mock = new Mock([
            new Response(200, [], Stream::factory($responseBody))
        ]);

        $client = new Client();
        $client->getEmitter()->attach($mock);

        $finder = new ExchangeRateFinder($client);
        $exchangeRates = $finder->findAll();

        $this->assertInternalType('array', $exchangeRates);
        $this->assertContainsOnlyInstancesOf(ExchangeRateInterface::class, $exchangeRates);
        $this->assertCount(22, $exchangeRates);

        // Check AUD
        $rate = $exchangeRates[0];
        $this->assertEquals('AUD', $rate->getCode());
        $this->assertEquals('AUSTRALIAN DOLLAR', $rate->getName());
        $this->assertEquals(10571.32, $rate->getSell());
        $this->assertEquals(10460.97, $rate->getBuy());
        $this->assertEquals((10571.32 + 10460.97) / 2, $rate->getMiddle());
        $this->assertEquals(\Carbon\Carbon::parse('11 November 2014'), $rate->getUpdatedAt());

        // Check JPY which have value 100
        $rate = $exchangeRates[9];
        $this->assertEquals('JPY', $rate->getCode());
        $this->assertEquals('JAPANESE YEN', $rate->getName());
        $this->assertEquals(10655.51/100, $rate->getSell());
        $this->assertEquals(10546.41/100, $rate->getBuy());
        $this->assertEquals((10655.51 + 10546.41) / 200, $rate->getMiddle());
        $this->assertEquals(\Carbon\Carbon::parse('11 November 2014'), $rate->getUpdatedAt());
    }
}
