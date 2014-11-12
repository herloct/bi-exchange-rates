<?php
namespace Kuartet\BI;

use \Kuartet\BI\Domain\RateInterface;
use \Kuartet\BI\Fetcher\Exception\ConnectionException;
use \Kuartet\BI\Fetcher\FetcherInterface;
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
     * @var ParserInterface
     */
    private $parser;

    /**
     * Constructor
     *
     * @param FetcherInterface $fetcher URL fetcher
     * @param ParserInterface  $parser  HTML parser
     */
    public function __construct(FetcherInterface $fetcher, ParserInterface $parser)
    {
        $this->fetcher = $fetcher;
        $this->parser = $parser;
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
        $html = $this->fetcher
            ->fetch(self::BASE_URL);

        $exchangeRates = $this->parser
            ->parse($html);

        return $exchangeRates;
    }
}
