<?php
namespace Kuartet\BI;

use \Carbon\Carbon;
use \GuzzleHttp\ClientInterface;
use \GuzzleHttp\Exception\TransferException;
use \RuntimeException;
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
     * @return Domain\RateInterface[] List of exchange rates
     * @throws RuntimeException       Connection Error
     * @throws RuntimeException       Page not found
     */
    public function findAll()
    {
        $html = null;

        try {
            $response = $this->client->get(self::BASE_URL);

            $html = (string) $response->getBody();
        } catch (TransferException $ex) {
            throw new RuntimeException('Connection problem', 0, $ex);
        }

        $exchangeRates = $this->parse($html);

        return $exchangeRates;
    }

    /**
     * Parse html response into exchange rates
     *
     * @param  string                 $html
     * @return Domain\RateInterface[]
     * @throws RuntimeException       Page not found
     */
    protected function parse($html)
    {
        $exchangeRates = [];

        if (mb_ereg("Sorry, the page you're looking for is not available", $html) !== false) {
            throw new \RuntimeException('Page not found', 1);
        }

        $crawler = new Crawler($html);
        $codes = $this->parseCurrencyCodeAndNames($crawler);
        $updatedAt = $this->parseLastUpdated($crawler);

        $that = $this;
        $crawler->filter('#ctl00_PlaceHolderMain_biWebKursTransaksiBI_GridView2 > tr')
            ->each(function (Crawler $tr, $i) use ($that, &$exchangeRates, $codes, $updatedAt) {
                if ($i > 0) {
                    $parts = [];
                    $tr->filter('td')->each(function ($td, $j) use (&$parts, $codes) {
                        $parts[] = $td->text();
                    });

                    $code = trim($parts[0]);
                    $name = $codes[$code];
                    $value = $that->getFloatFromString($parts[1]);
                    $sell = $that->getFloatFromString($parts[2]) / $value;
                    $buy = $that->getFloatFromString($parts[3]) / $value;
                    $exchangeRates[] = new Domain\Rate($code, $name, $sell, $buy, $updatedAt);
                }
            });

        return $exchangeRates;
    }

    /**
     * Find last updated info from html
     *
     * @param  Crawler $crawler
     * @return Carbon
     */
    protected function parseLastUpdated(Crawler $crawler)
    {
        $raw = $crawler->filter('#ctl00_PlaceHolderMain_biWebKursTransaksiBI_lblUpdate')
            ->text();
        $updatedAt = Carbon::parse($raw);

        return $updatedAt;
    }

    /**
     * Find array of currency code and name
     *
     * @param  Crawler $crawler
     * @return array   Currency code as key, currency name as value
     */
    protected function parseCurrencyCodeAndNames(Crawler $crawler)
    {
        $codes = [];
        $crawler->filter('#KodeSingkatan > div > table > tr')
            ->each(function (Crawler $tr, $i) use (&$codes) {
                if ($i > 0) {
                    $parts = [];
                    $tr->filter('td')->each(function ($td, $j) use (&$parts) {
                        $parts[] = $td->text();
                    });

                    $code = trim($parts[0]);
                    $name = trim($parts[1]);
                    $codes[$code] = $name;
                }
            });

        return $codes;
    }

    /**
     * Parse string to float
     *
     * @param  string $source
     * @return float
     */
    protected function getFloatFromString($source)
    {
        return floatval(mb_ereg_replace(',', '', $source));
    }
}
