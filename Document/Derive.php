<?php

namespace Heretique\DeriveSDK\Document;

class Derive
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $lat;
    
    /**
     * @var string
     */
    private $lng;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $address;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return self
     */
    public function setCode(string $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param float $lat
     * @return self
     */
    public function setLat($lat)
    {
        $this->lat = floatval($lat);

        return $this;
    }

    /**
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param float $lng
     * @return self
     */
    public function setLng($lng)
    {
        $this->lng = floatval($lng);

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return self
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

        /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $message
     * @return self
     */
    public function setAddress(string $address)
    {
        $this->address = $address;

        return $this;
    }
}