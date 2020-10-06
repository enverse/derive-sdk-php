<?php

namespace Heretique\DeriveSDK\Document;

class AddressText
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $country;

    /**
     * Get the value of prefix
     *
     * @return  string
     */ 
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the value of prefix
     *
     * @param  string  $prefix
     *
     * @return  self
     */ 
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get the value of street
     *
     * @return  string
     */ 
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set the value of street
     *
     * @param  string  $street
     *
     * @return  self
     */ 
    public function setStreet(string $street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get the value of city
     *
     * @return  string
     */ 
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @param  string  $city
     *
     * @return  self
     */ 
    public function setCity(string $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get the value of postalCode
     *
     * @return  string
     */ 
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set the value of postalCode
     *
     * @param  string  $postalCode
     *
     * @return  self
     */ 
    public function setPostalCode(string $postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get the value of country
     *
     * @return  string
     */ 
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the value of country
     *
     * @param  string  $country
     *
     * @return  self
     */ 
    public function setCountry(string $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'prefix' => $this->getPrefix(),
            'street' => $this->getStreet(),
            'city' => $this->getCity(),
            'postal_code' => $this->getPostalCode(),
            'country' => $this->getCountry()
        ];
    }
}