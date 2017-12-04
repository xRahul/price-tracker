<?php

namespace PriceTracker\bo;

class TrackingData {

	private $dateTime;
	private $buyPrice;
	private $sellPrice;

    public function toArray()
    {
        return [
            $this->getDateTime(),
            $this->getBuyPrice(),
            $this->getSellPrice()
        ];
    }

    public function isEmpty()
    {
        if (empty($this->getDateTime()) || empty($this->getBuyPrice()) || empty($this->getSellPrice())) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param mixed $dateTime
     *
     * @return self
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBuyPrice()
    {
        return $this->buyPrice;
    }

    /**
     * @param mixed $buyPrice
     *
     * @return self
     */
    public function setBuyPrice($buyPrice)
    {
        $this->buyPrice = $buyPrice;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSellPrice()
    {
        return $this->sellPrice;
    }

    /**
     * @param mixed $sellPrice
     *
     * @return self
     */
    public function setSellPrice($sellPrice)
    {
        $this->sellPrice = $sellPrice;

        return $this;
    }
}