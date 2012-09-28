<?php
###############################
# AUTOGENERATED - DO NOT EDIT #
###############################

namespace CMF\Runtime\Domain\Shofi\Place;
use CMF\Core\Runtime\Document;

abstract class BaseLocationDocument extends Document\Document
{
    public function getStreet()
    {
        return $this->getValue('street');
    }

    public function setStreet($street)
    {
        $this->setValue('street', $street);
    }

    public function getHouseNumber()
    {
        return $this->getValue('houseNumber');
    }

    public function setHouseNumber($houseNumber)
    {
        $this->setValue('houseNumber', $houseNumber);
    }

    public function getPostalCode()
    {
        return $this->getValue('postalCode');
    }

    public function setPostalCode($postalCode)
    {
        $this->setValue('postalCode', $postalCode);
    }

    public function getCity()
    {
        return $this->getValue('city');
    }

    public function setCity($city)
    {
        $this->setValue('city', $city);
    }

    public function getDistrict()
    {
        return $this->getValue('district');
    }

    public function setDistrict($district)
    {
        $this->setValue('district', $district);
    }

    public function getName()
    {
        return $this->getValue('name');
    }

    public function setName($name)
    {
        $this->setValue('name', $name);
    }
}
