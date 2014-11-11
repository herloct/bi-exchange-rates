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
    }
}
