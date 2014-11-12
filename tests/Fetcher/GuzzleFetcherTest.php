<?php
namespace Kuartet\BI\Fetcher;

use \GuzzleHttp\Client;
use \GuzzleHttp\Message\Response;
use \GuzzleHttp\Stream\Stream;
use \GuzzleHttp\Subscriber\Mock;
use \PHPUnit_Framework_TestCase;

class GuzzleFetcherTest extends PHPUnit_Framework_TestCase
{
    public function testFetch()
    {
        $responseBody = 'someresults';

        $mock = new Mock([
            new Response(200, [], Stream::factory($responseBody))
        ]);

        $client = new Client();
        $client->getEmitter()->attach($mock);

        $fetcher = new GuzzleFetcher($client);
        $html = $fetcher->fetch('someurl');

        $this->assertInstanceOf('Kuartet\BI\Fetcher\FetcherInterface', $fetcher);
        $this->assertEquals($responseBody, $html);
    }
}
