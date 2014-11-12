<?php
namespace Kuartet\BI;

use \GuzzleHttp\Client;
use \Kuartet\BI\Domain\RateInterface;
use \Kuartet\BI\Fetcher\Exception\ConnectionException;
use \Kuartet\BI\Fetcher\FetcherInterface;
use \Kuartet\BI\Fetcher\GuzzleFetcher;
use \Kuartet\BI\Parser\DomCrawlerParser;
use \Kuartet\BI\Parser\Exception\ParseException;
use \Kuartet\BI\Parser\ParserInterface;

/**
 * Fetch and Parse exchange rates from Bank Indonesia
 *
 * @author herloct <herloct@gmail.com>
 */
class ExchangeRate
{
    /**
     * Bank Indonesia exchange rates URL
     */
    const BASE_URL = 'http://www.bi.go.id/en/moneter/informasi-kurs/transaksi-bi/Default.aspx';

    /**
     * @var FetcherInterface
     */
    private $fetcher;

    /**
     * Get URL fetcher
     *
     * @return FetcherInterface
     */
    public function getFetcher()
    {
        if (! $this->fetcher) {
            $client = new Client();
            $fetcher = new GuzzleFetcher($client);

            $this->setFetcher($fetcher);
        }

        return $this->fetcher;
    }

    /**
     * Set URL fetcher
     *
     * @param FetcherInterface $fetcher URL fetcher
     * @return ExchangeRate
     */
    public function setFetcher(FetcherInterface $fetcher)
    {
        $this->fetcher = $fetcher;

        return $this;
    }

    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * Get HTML parser
     *
     * @return ParserInterface
     */
    public function getParser()
    {
        if (! $this->parser) {
            $parser = new DomCrawlerParser();

            $this->setParser($parser);
        }

        return $this->parser;
    }

    /**
     * Set HTML parser
     *
     * @param ParserInterface $parser HTML parser
     * @return ExchangeRate
     */
    public function setParser(ParserInterface $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Fetch and parse Bank Indonesia site for Exchange rates
     *
     * @return RateInterface[]     List of exchange rates
     * @throws ConnectionException Connection Error
     * @throws ParseException      Invalid HTML structure
     */
    public function getUpdates()
    {
        $html = $this->getFetcher()
            ->fetch(self::BASE_URL);

        $exchangeRates = $this->getParser()
            ->parse($html);

        return $exchangeRates;
    }
}
