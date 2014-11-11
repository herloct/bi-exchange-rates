<?php
namespace Kuartet\BI\ExchangeRate;

use \Carbon\Carbon;
use \GuzzleHttp\Client;
use \GuzzleHttp\Message\Response;
use \GuzzleHttp\Stream\Stream;
use \GuzzleHttp\Subscriber\Mock;
use \PHPUnit_Framework_TestCase;

class ExchangeRateFinderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderTestFindAll
     */
    public function testFindAll($index, $code, $name, $sell, $buy, $middle, $updatedAt)
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
        $rate = $exchangeRates[$index];
        $this->assertEquals($code, $rate->getCode());
        $this->assertEquals($name, $rate->getName());
        $this->assertEquals($sell, $rate->getSell());
        $this->assertEquals($buy, $rate->getBuy());
        $this->assertEquals($middle, $rate->getMiddle());
        $this->assertEquals(Carbon::parse($updatedAt), $rate->getUpdatedAt());
    }

    public function dataProviderTestFindAll()
    {
        return [
            [0, 'AUD', 'AUSTRALIAN DOLLAR', 10571.32, 10460.97, (10571.32 + 10460.97) / 2, '11 November 2014'],
            [9, 'JPY', 'JAPANESE YEN', 10655.51/100, 10546.41/100, (10655.51 + 10546.41) / 200, '11 November 2014'],
            [21, 'USD', 'US DOLLAR', 12224.00, 12102.00, (12224.00 + 12102.00) / 2, '11 November 2014']
        ];
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFindAllPageNotFound()
    {
        $responseBody = file_get_contents(__DIR__.'/resources/finder_findAll_not_found.html');

        $mock = new Mock([
            new Response(200, [], Stream::factory($responseBody))
        ]);

        $client = new Client();
        $client->getEmitter()->attach($mock);

        $finder = new ExchangeRateFinder($client);
        $finder->findAll();
    }

    /**
     * @dataProvider dataProviderTestFindAllWithConnectionProblem
     * @expectedException \RuntimeException
     */
    public function testFindAllWithConnectionProblem($responses)
    {
        $mock = new Mock($responses);

        $client = new Client();
        $client->getEmitter()->attach($mock);

        $finder = new ExchangeRateFinder($client);
        $finder->findAll();
    }

    public function dataProviderTestFindAllWithConnectionProblem()
    {
        return [
            [[new Response(503)]],
            [[new Response(500)]],
            [[new Response(404)]],
            [[new Response(403)]],
            [[new Response(401)]],
            [[]]
        ];
    }
}
