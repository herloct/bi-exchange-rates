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

        $url = 'http://domain.com';

        $fetcher = new GuzzleFetcher($client);
        $html = $fetcher->fetch($url);

        $this->assertInstanceOf('Kuartet\BI\Fetcher\FetcherInterface', $fetcher);
        $this->assertEquals($responseBody, $html);
    }

    /**
     * @dataProvider dataProviderTestFetchWithConnectionProblem
     * @expectedException \Kuartet\BI\Fetcher\Exception\ConnectionException
     */
    public function testFetchWithConnectionProblem($responses)
    {
        $mock = new Mock($responses);

        $client = new Client();
        $client->getEmitter()->attach($mock);

        $url = 'http://domain.com';

        $fetcher = new GuzzleFetcher($client);
        $fetcher->fetch($url);
    }

    public function dataProviderTestFetchWithConnectionProblem()
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
