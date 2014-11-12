<?php
namespace Kuartet\BI\Parser;

use \Carbon\Carbon;
use \PHPUnit_Framework_TestCase;

class DomCrawlerParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderTestParse
     */
    public function testParse($index, $code, $name, $sell, $buy, $middle, $updatedAt)
    {
        $html = file_get_contents(__DIR__.'/../resources/finder_findAll.html');

        $parser = new DomCrawlerParser();
        $this->assertInstanceOf('\Kuartet\BI\Parser\ParserInterface', $parser);

        $rates = $parser->parse($html);

        $this->assertInternalType('array', $rates);
        $this->assertContainsOnlyInstancesOf('\Kuartet\BI\Domain\RateInterface', $rates);
        $this->assertCount(22, $rates);

        $rate = $rates[$index];
        $this->assertEquals($code, $rate->getCode());
        $this->assertEquals($name, $rate->getName());
        $this->assertEquals($sell, $rate->getSell());
        $this->assertEquals($buy, $rate->getBuy());
        $this->assertEquals($middle, $rate->getMiddle());
        $this->assertEquals(Carbon::parse($updatedAt), $rate->getUpdatedAt());
    }

    public function dataProviderTestParse()
    {
        return [
            [0, 'AUD', 'AUSTRALIAN DOLLAR', 10571.32, 10460.97, (10571.32 + 10460.97) / 2, '11 November 2014'],
            [9, 'JPY', 'JAPANESE YEN', 10655.51/100, 10546.41/100, (10655.51 + 10546.41) / 200, '11 November 2014'],
            [21, 'USD', 'US DOLLAR', 12224.00, 12102.00, (12224.00 + 12102.00) / 2, '11 November 2014']
        ];
    }

    /**
     * @dataProvider dataProviderTestParseWithInvalidHtml
     * @expectedException \Kuartet\BI\Parser\Exception\ParseException
     */
    public function testParseWithInvalidHtml($html)
    {
        $parser = new DomCrawlerParser();
        $parser->parse($html);
    }

    public function dataProviderTestParseWithInvalidHtml()
    {
        return [
            [file_get_contents(__DIR__.'/../resources/finder_findAll_not_found.html')]
        ];
    }
}
