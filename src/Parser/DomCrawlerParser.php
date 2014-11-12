<?php
namespace Kuartet\BI\Parser;

use \Carbon\Carbon;
use \InvalidArgumentException;
use \Kuartet\BI\Domain\Rate;
use \Kuartet\BI\Domain\RateInterface;
use \Kuartet\BI\Parser\Exception\ParseException;
use \Symfony\Component\DomCrawler\Crawler;

/**
 * HTML parser using Symfony/DomCrawler
 *
 * @author herloct <herloct@gmail.com>
 */
class DomCrawlerParser implements ParserInterface
{
    /**
     * Parse HTML to Rates
     *
     * @param  string          $html HTML to parse
     * @return RateInterface[] Array of rates
     * @throws ParseException  Invalid HTML source
     */
    public function parse($html)
    {
        $exchangeRates = [];

        try {
            $crawler = new Crawler($html);
            $codes = $this->parseCurrencyCodeAndNames($crawler);
            $updatedAt = $this->parseLastUpdated($crawler);

            // ugh ugly patch because HHVM can't access private method inside closure
            $parseFloat = function($source) {
                return floatval(mb_ereg_replace(',', '', $source));
            };
            $crawler->filter('#ctl00_PlaceHolderMain_biWebKursTransaksiBI_GridView2 > tr')
                ->each(function (Crawler $tr, $i) use (&$exchangeRates, $codes, $parseFloat, $updatedAt) {
                    if ($i > 0) {
                        $parts = [];
                        $tr->filter('td')->each(function ($td, $j) use (&$parts, $codes) {
                            $parts[] = $td->text();
                        });

                        $code = trim($parts[0]);
                        $name = $codes[$code];
                        $value = $parseFloat($parts[1]);
                        $sell = $parseFloat($parts[2]) / $value;
                        $buy = $parseFloat($parts[3]) / $value;
                        $exchangeRates[] = new Rate($code, $name, $sell, $buy, $updatedAt);
                    }
                });
        } catch (InvalidArgumentException $ex) {
            throw new ParseException('Invalid HTML source', $ex);
        }

        return $exchangeRates;
    }

    /**
     * Find array of currency code and name
     *
     * @param  Crawler $crawler
     * @return array   Currency code as key, currency name as value
     */
    private function parseCurrencyCodeAndNames(Crawler $crawler)
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
     * Find last updated info from html
     *
     * @param  Crawler $crawler
     * @return Carbon
     */
    private function parseLastUpdated(Crawler $crawler)
    {
        $raw = $crawler->filter('#ctl00_PlaceHolderMain_biWebKursTransaksiBI_lblUpdate')
            ->text();
        $updatedAt = Carbon::parse($raw);

        return $updatedAt;
    }
}
