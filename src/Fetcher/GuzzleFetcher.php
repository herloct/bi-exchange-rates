<?php
namespace Kuartet\BI\Fetcher;

use \GuzzleHttp\ClientInterface;

/**
 * URL fetcher using Guzzle
 *
 * @author herloct <herloct@gmail.com>
 */
class GuzzleFetcher implements FetcherInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch URL
     *
     * @param string $url
     * @return string HTML string
     */
    public function fetch($url)
    {
       $response = $this->client->get($url);

       return (string) $response->getBody();
    }
}
