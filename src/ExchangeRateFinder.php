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
            $response = $this->client->get();

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
        $codes = $this->parseCurrencyCodeAndNames($html);
        $crawler = new Crawler($html);

        $crawler->filter('#ctl00_PlaceHolderMain_biWebKursTransaksiBI_GridView1 > tbody > tr')
            ->each(function(Crawler $tr, $i) use(&$exchangeRates, $codes)
            {
                if ($i > 0) {
                    $parts = [];
                    $tr->filter('td')->each(function($td, $j) use(&$parts, $codes)
                    {
                        $parts[] = $td->text();
                    });

                    $code = trim($parts[0]);
                    $name = $codes[$code];
                    $value = floatval($parts[1]);
                    $sell = floatval($parts[2]) / $value;
                    $buy = floatval($parts[3]) / $value;
                    $exchangeRates[] = new ExchangeRate($code, $name, $sell, $buy, Carbon::now());
                }
            });

        return $exchangeRates;
    }

    protected function parseCurrencyCodeAndNames($html)
    {
        $codes = [];
        $crawler = new Crawler($html);

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
}
