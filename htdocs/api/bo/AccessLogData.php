<?php

namespace PriceTracker\bo;

class AccessLogData {
	private $dateTime;
	private $timeTaken;
	private $fromIPAddress;

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
    public function getTimeTaken()
    {
        return $this->timeTaken;
    }

    /**
     * @param mixed $timeTaken
     *
     * @return self
     */
    public function setTimeTaken($timeTaken)
    {
        $this->timeTaken = $timeTaken;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFromIPAddress()
    {
        return $this->fromIPAddress;
    }

    /**
     * @param mixed $fromIPAddress
     *
     * @return self
     */
    public function setFromIPAddress($fromIPAddress)
    {
        $this->fromIPAddress = $fromIPAddress;

        return $this;
    }

    public function toArray()
    {
        return [
            $this->getDateTime(),
            $this->getTimeTaken(),
            $this->getFromIPAddress()
        ];
    }
}