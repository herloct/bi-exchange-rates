<?php
namespace Kuartet\BI\Fetcher;

use \GuzzleHttp\ClientInterface;
use \GuzzleHttp\Exception\TransferException;

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

    /**
     * Constructor
     *
     * @param ClientInterface $client Guzzle client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch URL
     *
     * @param  string                        $url URL to fetch
     * @return string                        HTML string
     * @throws Exception\ConnectionException Connection error, http code 5xx, 4xx
     */
    public function fetch($url)
    {
        $html = null;

        try {
            $response = $this->client->get($url);

            $html = (string) $response->getBody();
        } catch (TransferException $ex) {
            throw new Exception\ConnectionException("Cannot connect to {$url}", $ex);
        }

        return $html;
    }
}
