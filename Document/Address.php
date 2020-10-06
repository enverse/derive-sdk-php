<?php

namespace Heretique\DeriveSDK\Document;

class Address
{
    /**
     * @var Position
     */
    private $position;

    /**
     * @var AddressText
     */
    private $text;

    /**
     * Get the value of position
     *
     * @return  Position
     */ 
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of position
     *
     * @param  Position  $position
     *
     * @return  self
     */ 
    public function setPosition(Position $position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the value of text
     *
     * @return  AddressText
     */ 
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set the value of text
     *
     * @param  AddressText  $text
     *
     * @return  self
     */ 
    public function setText(AddressText $text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return float
     */
    public function getPositionLat()
    {
        if ($this->getPosition()) {
            return $this->getPosition()->getLat();
        }

        return null;
    }

    /**
     * @return float
     */
    public function getPositionLng()
    {
        if ($this->getPosition()) {
            return $this->getPosition()->getLng();
        }

        return null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'text' => $this->getText()->toArray(),
            'position' => $this->getPosition()->toArray()
        ];
    }
}