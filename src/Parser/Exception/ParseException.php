<?php
namespace Kuartet\BI\Parser\Exception;

use \Exception;
use \RuntimeException;

/**
 * Parse exception
 *
 * For invalid html source
 *
 * @author herloct
 */
class ParseException extends RuntimeException
{
    /**
     * Constructor
     *
     * @param string         $message  Exception message
     * @param null|Exception $previous Previous exception
     */
    public function __construct($message, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
