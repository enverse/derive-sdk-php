<?php

namespace Heretique\DeriveSDK\Factory;

use Heretique\DeriveSDK\Document\Address;
use Heretique\DeriveSDK\Document\AddressText;
use Heretique\DeriveSDK\Document\Derive;
use Heretique\DeriveSDK\Document\Position;
use Heretique\DeriveSDK\Exception\DeriveFactoryException;

class DeriveFactory
{
    public static function createDeriveFromArray($deriveAsArray)
    {
        $derive = new Derive();

        if (!isset($deriveAsArray['message'])) {
            throw new DeriveFactoryException('Missing message key in derive as array');
        }

        $address = self::createAddressFromArray($deriveAsArray);
        $derive->setAddress($address);
        $derive->setMessage($deriveAsArray['message']);

        if (isset($deriveAsArray['reveal_address'])) {
            $derive->setRevealAddress($deriveAsArray['reveal_address']);
        }

        if (isset($deriveAsArray['code'])) {
            $derive->setCode($deriveAsArray['code']);
        }

        return $derive;
    }

    public static function createAddressFromArray($deriveAsArray)
    {
        if (!isset($deriveAsArray['address'])) {
            throw new DeriveFactoryException('Missing address key in derive as array');
        }

        $addressAsArray = $deriveAsArray['address'];

        $position = self::createPositionFromArray($addressAsArray);
        $addressText = self::createAddressTextFromArray($addressAsArray);

        $address = new Address();
        $address->setPosition($position);
        $address->setText($addressText);

        return $address;
    }

    public static function createPositionFromArray($addressAsArray)
    {
        if (!isset($addressAsArray['position'])) {
            throw new DeriveFactoryException('Missing position key in address as array');
        }

        $positionAsArray = $addressAsArray['position'];

        if (!isset($positionAsArray['lat']) || !isset($positionAsArray['lng'])) {
            throw new DeriveFactoryException('Missing lat or lng key in position as array');
        }

        $position = new Position();
        $position->setLat($positionAsArray['lat']);
        $position->setLng($positionAsArray['lng']);

        return $position;
    }

    public static function createAddressTextFromArray($addressAsArray) 
    {
        if (!isset($addressAsArray['text'])) {
            throw new DeriveFactoryException('Missing text key in address as array');
        }

        $textAsArray = $addressAsArray['text'];
        $addressText = new AddressText();
        
        if (isset($textAsArray['prefix'])) {
            $addressText->setPrefix($textAsArray['prefix']);
        }

        if (isset($textAsArray['street'])) {
            $addressText->setStreet($textAsArray['street']);
        }

        if (isset($textAsArray['city'])) {
            $addressText->setCity($textAsArray['city']);
        }

        if (isset($textAsArray['postal_code'])) {
            $addressText->setPostalCode($textAsArray['postal_code']);
        }

        if (isset($textAsArray['country'])) {
            $addressText->setCountry($textAsArray['country']);
        }

        return $addressText;
    }
}