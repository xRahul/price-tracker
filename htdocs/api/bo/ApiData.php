<?php

namespace PriceTracker\bo;

class ApiData {

	private $name;
	private $url;
	private $buyKey;
	private $sellKey;


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBuyKey()
    {
        return $this->buyKey;
    }

    /**
     * @param mixed $buyKey
     *
     * @return self
     */
    public function setBuyKey($buyKey)
    {
        $this->buyKey = $buyKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSellKey()
    {
        return $this->sellKey;
    }

    /**
     * @param mixed $sellKey
     *
     * @return self
     */
    public function setSellKey($sellKey)
    {
        $this->sellKey = $sellKey;

        return $this;
    }
}