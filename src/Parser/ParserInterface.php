<?php
namespace Kuartet\BI\Parser;

use \Kuartet\BI\Domain\RateInterface;
use \Kuartet\BI\Parser\Exception\ParseException;

/**
 * HTML parser interface
 *
 * @author herloct <herloct@gmail.com>
 */
interface ParserInterface
{
    /**
     * Parse HTML to Rates
     *
     * @param  string          $html HTML to parse
     * @return RateInterface[] Array of rates
     * @throws ParseException  Invalid HTML source
     */
    public function parse($html);
}
