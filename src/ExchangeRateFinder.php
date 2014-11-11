<?php
namespace Kuartet\BI\ExchangeRate;

use \Carbon\Carbon;
use \GuzzleHttp\ClientInterface;
use \GuzzleHttp\Exception\TransferException;
use \Symfony\Component\DomCrawler\Crawler;

/**
 * Parse exchange rates from Bank Indonesia
 *
 * @author herloct <herloct@gmail.com>
 */
class ExchangeRateFinder
{
    /**
     * Bank Indonesia exchange rates URL
     */
    const BASE_URL = 'http://www.bi.go.id/en/moneter/informasi-kurs/transaksi-bi/Default.aspx';

    /**
     *
     * @var ClientInterface
     */
    private $client;

    /**
     *
     * @param ClientInterface $client Guzzle client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch and parse Bank Indonesia site for Exchange rates
     *
     * @return ExchangeRateInterface[] List of exchange rates
     */
    public function findAll()
    {
        $exchangeRates = [];

        try {
            $response = $this->client->get(self::BASE_URL);

            $html = (string) $response->getBody();
            $exchangeRates = $this->parse($html);
        } catch (TransferException $ex) {
            throw new \RuntimeException('Connection problem', 0, $ex);
        }

        return $exchangeRates;
    }

    protected function parse($html)
    {
        $exchangeRates = [];
        $crawler = new Crawler($html);
        $codes = $this->parseCurrencyCodeAndNames($crawler);
        $updatedAt = $this->parseLastUpdated($crawler);

        $that = $this;
        $crawler->filter('#ctl00_PlaceHolderMain_biWebKursTransaksiBI_GridView2 > tbody > tr')
            ->each(function(Crawler $tr, $i) use($that, &$exchangeRates, $codes, $updatedAt)
            {
                if ($i > 0) {
                    $parts = [];
                    $tr->filter('td')->each(function($td, $j) use(&$parts, $codes)
                    {
                        $parts[] = $td->text();
                    });

                    $code = trim($parts[0]);
                    $name = $codes[$code];
                    $value = $that->getDoubleFromString($parts[1]);
                    $sell = $that->getDoubleFromString($parts[2]) / $value;
                    $buy = $that->getDoubleFromString($parts[3]) / $value;
                    $exchangeRates[] = new ExchangeRate($code, $name, $sell, $buy, $updatedAt);
                }
            });

        return $exchangeRates;
    }

    protected function parseLastUpdated(Crawler $crawler)
    {
        $raw = $crawler->filter('#ctl00_PlaceHolderMain_biWebKursTransaksiBI_lblUpdate')
            ->text();
        $updatedAt = Carbon::parse($raw);

        return $updatedAt;
    }

    protected function parseCurrencyCodeAndNames(Crawler $crawler)
    {
        $codes = [];
        $crawler->filter('#KodeSingkatan > div > table > tbody > tr')
            ->each(function(Crawler $tr, $i) use(&$codes)
            {
                if ($i > 0) {
                    $parts = [];
                    $tr->filter('td')->each(function($td, $j) use(&$parts)
                    {
                        $parts[] = $td->text();
                    });

                    $code = trim($parts[0]);
                    $name = trim($parts[1]);
                    $codes[$code] = $name;
                }
            });

        return $codes;
    }

    protected function getDoubleFromString($source)
    {
        return floatval(mb_ereg_replace(',', '', $source));
    }
}
