<?php

namespace Dat0r\Runtime\Attribute\Image;

use Assert;
use Dat0r\Common\Error\BadValueException;

class Image
{
    const PROPERTY_STORAGE_LOCATION = 'storage_location';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CAPTION = 'caption';
    const PROPERTY_COPYRIGHT = 'copyright';
    const PROPERTY_COPYRIGHT_URL = 'copyright_url';
    const PROPERTY_SOURCE = 'source';
    const PROPERTY_META_DATA = 'meta_data';

    protected $storage_location = '';
    protected $title = '';
    protected $caption = '';
    protected $copyright = '';
    protected $copyright_url = '';
    protected $source = '';
    protected $meta_data = [];

    public function __construct(
        $storage_location,
        $title = '',
        $caption = '',
        $copyright = '',
        $copyright_url = '',
        $source = '',
        array $meta_data = []
    ) {
        Assert\that($storage_location)->string()->notEmpty();
        Assert\that($title)->string();
        Assert\that($caption)->string();
        Assert\that($copyright)->string();
        Assert\that($copyright_url)->string();
        Assert\that($source)->string();

        $this->storage_location = $storage_location;
        $this->title = $title;
        $this->caption = $caption;
        $this->copyright = $copyright;
        $this->copyright_url = $copyright_url;
        $this->source = $source;
        $this->meta_data = $meta_data;
    }

    public static function createFromImage(Image $img)
    {
        return new Image(
            $img->getStorageLocation(),
            $img->getTitle(),
            $img->getCaption(),
            $img->getCopyright(),
            $img->getCopyrightUrl(),
            $img->getSource(),
            $img->getMetaData()
        );
    }

    public static function createFromArray(array $img)
    {
        if (array_key_exists(self::PROPERTY_STORAGE_LOCATION, $img)) {
            $storage_location = $img[self::PROPERTY_STORAGE_LOCATION];
            Assert\that($storage_location)->string()->notEmpty();
        } else {
            throw new BadValueException('No ' . self::PROPERTY_STORAGE_LOCATION . ' given.');
        }


        $title = '';
        if (array_key_exists(self::PROPERTY_TITLE, $img)) {
            $title = $img[self::PROPERTY_TITLE];
        }

        $caption = '';
        if (array_key_exists(self::PROPERTY_CAPTION, $img)) {
            $caption = $img[self::PROPERTY_CAPTION];
        }

        $copyright = '';
        if (array_key_exists(self::PROPERTY_COPYRIGHT, $img)) {
            $copyright = $img[self::PROPERTY_COPYRIGHT];
        }

        $copyright_url = '';
        if (array_key_exists(self::PROPERTY_COPYRIGHT_URL, $img)) {
            $copyright_url = $img[self::PROPERTY_COPYRIGHT_URL];
        }

        $source = '';
        if (array_key_exists(self::PROPERTY_SOURCE, $img)) {
            $source = $img[self::PROPERTY_SOURCE];
        }

        $meta_data = [];
        if (array_key_exists(self::PROPERTY_META_DATA, $img)) {
            $meta_data = $img[self::PROPERTY_META_DATA];
        }

        return new Image(
            $storage_location,
            $title,
            $caption,
            $copyright,
            $copyright_url,
            $source,
            $meta_data
        );
    }

    public function getStorageLocation()
    {
        return $this->storage_location;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function getCopyright()
    {
        return $this->copyright;
    }

    public function getCopyrightUrl()
    {
        return $this->copyright_url;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getMetaData()
    {
        return $this->meta_data;
    }

    /**
     * Returns a (de)serializable representation of the internal value.
     *
     * @return array value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        return [
            'storage_location' => $this->storage_location,
            'title' => $this->title,
            'caption' => $this->caption,
            'copyright' => $this->copyright,
            'copyright_url' => $this->copyright_url,
            'source' => $this->source,
            'meta_data' => $this->meta_data // TODO hopefully only scalar content?
        ];
    }

    public function __toString()
    {
        return $this->storage_location;
    }
}
