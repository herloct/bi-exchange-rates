<?php
namespace Kuartet\BI\Fetcher;

/**
 * URL fetcher interface
 *
 * @author herloct <herloct@gmail.com>
 */
interface FetcherInterface
{
    /**
     * Fetch URL
     *
     * @param  string                        $url URL to fetch
     * @return string                        HTML string
     * @throws Exception\ConnectionException Connection error, http code 5xx, 4xx
     */
    public function fetch($url);
}
