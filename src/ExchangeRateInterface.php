<?php
namespace Kuartet\BI\ExchangeRate;

use \Carbon\Carbon;

/**
 * Exchange rate
 *
 * @author herloct <herloct@gmail.com>
 */
interface ExchangeRateInterface
{
    /**
     * Get currency code
     *
     * Currency code follows ISO 4217
     *
     * @return string
     */
    function getCode();

    /**
     * Get currency name
     *
     * Currency name follows ISO 4217
     *
     * @return string
     */
    function getName();

    /**
     * Get sell value
     *
     * @return float
     */
    function getSell();

    /**
     * Get buy value
     *
     * @return float
     */
    function getBuy();

    /**
     * Get middle value
     *
     * @return float
     */
    function getMiddle();

    /**
     * Get updated at
     *
     * @return Carbon
     */
    function getUpdatedAt();
}
