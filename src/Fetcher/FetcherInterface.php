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
     * @param string $url
     * @return string HTML string
     */
    public function fetch($url);
}
