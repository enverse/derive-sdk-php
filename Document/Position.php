<?php

namespace Heretique\DeriveSDK\Document;

class Position
{
    /**
     * @var float
     */
    private $lat;
    
    /**
     * @var float
     */
    private $lng;

    /**
     * Get the value of lat
     *
     * @return  float
     */ 
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set the value of lat
     *
     * @param  float  $lat
     *
     * @return  self
     */ 
    public function setLat(float $lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get the value of lng
     *
     * @return  float
     */ 
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set the value of lng
     *
     * @param  float  $lng
     *
     * @return  self
     */ 
    public function setLng(float $lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'lat' => $this->getLat(),
            'lng' => $this->getLng()
        ];
    }
}