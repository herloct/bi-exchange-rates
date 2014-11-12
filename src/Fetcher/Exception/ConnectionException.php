<?php
namespace Kuartet\BI\Fetcher\Exception;

use \Exception;
use \RuntimeException;

/**
 * Connection exception
 *
 * For handling connection error, http code 5xx, and http code 4xx
 *
 * @author herloct
 */
class ConnectionException extends RuntimeException
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
