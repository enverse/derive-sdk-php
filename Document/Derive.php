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
    private $message;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var boolean
     */
    private $revealAddress;

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
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $message
     * @return self
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of revealAddress
     *
     * @return  boolean
     */ 
    public function getRevealAddress()
    {
        return $this->revealAddress;
    }

    /**
     * Set the value of revealAddress
     *
     * @param  bool  $revealAddress
     *
     * @return  self
     */ 
    public function setRevealAddress(bool $revealAddress)
    {
        $this->revealAddress = $revealAddress;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAddressPositionLat()
    {
        if ($this->getAddress()) {
            return $this->getAddress()->getPositionLat();
        }

        return null;
    }

    /**
     * @return float|null
     */
    public function getAddressPositionLng()
    {
        if ($this->getAddress()) {
            return $this->getAddress()->getPositionLng();
        }

        return null;
    }

    /**
     * @return AddressText|null
     */
    public function getAddressText()
    {
        if ($this->getAddress()) {
            return $this->getAddress()->getText();
        }

        return null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'address' => $this->getAddress()->toArray(),
            'reveal_address' => $this->getRevealAddress(),
            'message' => $this->getMessage(),
            'code' => $this->getCode()
        ];
    }
}