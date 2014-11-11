<?php
namespace Kuartet\BI\ExchangeRate;

use \Carbon\Carbon;

/**
 * Exchange rate
 *
 * @author herloct <herloct@gmail.com>
 */
class ExchangeRate implements ExchangeRateInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * Get currency code
     *
     * Currency code follows ISO 4217
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @var string
     */
    private $name;

    /**
     * Get currency name
     *
     * Currency name follows ISO 4217
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @var float
     */
    private $sell;

    /**
     * Get sell value
     *
     * @return float
     */
    public function getSell()
    {
        return $this->sell;
    }

    /**
     * @var float
     */
    private $buy;

    /**
     * Get buy value
     *
     * @return float
     */
    public function getBuy()
    {
        return $this->buy;
    }

    /**
     * Get middle value
     *
     * @return float
     */
    public function getMiddle()
    {
        return ($this->getSell() + $this->getBuy()) / 2;
    }

    /**
     * @var Carbon
     */
    private $updatedAt;

    /**
     * Get updated at
     *
     * @return Carbon
     */
    public function getUpdatedAt()
    {
        return clone $this->updatedAt;
    }

    /**
     * Constructor
     *
     * @param string $code      Currency code
     * @param string $name      Currency name
     * @param float $sell      Sell value
     * @param float $buy       Buy value
     * @param Carbon $updatedAt Updated at
     */
    public function __construct($code, $name, $sell, $buy, Carbon $updatedAt)
    {
        $this->code = $code;
        $this->name = $name;
        $this->sell = $sell;
        $this->buy = $buy;
        $this->updatedAt = $updatedAt;
    }
}
