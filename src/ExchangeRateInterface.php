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
    public function getCode();

    /**
     * Get currency name
     *
     * Currency name follows ISO 4217
     *
     * @return string
     */
    public function getName();

    /**
     * Get sell value
     *
     * @return float
     */
    public function getSell();

    /**
     * Get buy value
     *
     * @return float
     */
    public function getBuy();

    /**
     * Get middle value
     *
     * @return float
     */
    public function getMiddle();

    /**
     * Get updated at
     *
     * @return Carbon
     */
    public function getUpdatedAt();
}
