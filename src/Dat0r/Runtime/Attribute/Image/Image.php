<?php

namespace Dat0r\Runtime\Attribute\Image;

use Assert;
use Dat0r\Common\Error\BadValueException;

class Image
{
    const PROPERTY_LOCATION = 'location';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CAPTION = 'caption';
    const PROPERTY_COPYRIGHT = 'copyright';
    const PROPERTY_COPYRIGHT_URL = 'copyright_url';
    const PROPERTY_SOURCE = 'source';
    const PROPERTY_META_DATA = 'meta_data';

    protected $location = '';
    protected $title = '';
    protected $caption = '';
    protected $copyright = '';
    protected $copyright_url = '';
    protected $source = '';
    protected $meta_data = [];

    public function __construct(
        $location,
        $title = '',
        $caption = '',
        $copyright = '',
        $copyright_url = '',
        $source = '',
        array $meta_data = []
    ) {
        Assert\that($location)->string()->notEmpty();
        Assert\that($title)->string();
        Assert\that($caption)->string();
        Assert\that($copyright)->string();
        Assert\that($copyright_url)->string();
        Assert\that($source)->string();

        $this->location = $location;
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
            $img->getLocation(),
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
        if (array_key_exists(self::PROPERTY_LOCATION, $img)) {
            $location = $img[self::PROPERTY_LOCATION];
            Assert\that($location)->string()->notEmpty();
        } else {
            throw new BadValueException('No image ' . self::PROPERTY_LOCATION . ' given.');
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
            $location,
            $title,
            $caption,
            $copyright,
            $copyright_url,
            $source,
            $meta_data
        );
    }

    public function getLocation()
    {
        return $this->location;
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
            self::PROPERTY_LOCATION => $this->location,
            self::PROPERTY_TITLE => $this->title,
            self::PROPERTY_CAPTION => $this->caption,
            self::PROPERTY_COPYRIGHT => $this->copyright,
            self::PROPERTY_COPYRIGHT_URL => $this->copyright_url,
            self::PROPERTY_SOURCE => $this->source,
            self::PROPERTY_META_DATA => $this->meta_data // TODO hopefully only scalar content?
        ];
    }

    public function similarToArray(array $other)
    {
        $equal = array_key_exists(self::PROPERTY_LOCATION, $other) &&
            $other[self::PROPERTY_LOCATION] === $this->getLocation() &&
            array_key_exists(self::PROPERTY_TITLE, $other) &&
            $other[self::PROPERTY_TITLE] === $this->getTitle() &&
            array_key_exists(self::PROPERTY_CAPTION, $other) &&
            $other[self::PROPERTY_CAPTION] === $this->getCaption() &&
            array_key_exists(self::PROPERTY_COPYRIGHT, $other) &&
            $other[self::PROPERTY_COPYRIGHT] === $this->getCopyright() &&
            array_key_exists(self::PROPERTY_COPYRIGHT_URL, $other) &&
            $other[self::PROPERTY_COPYRIGHT_URL] === $this->getCopyrightUrl() &&
            array_key_exists(self::PROPERTY_SOURCE, $other) &&
            $other[self::PROPERTY_SOURCE] === $this->getSource() &&
            array_key_exists(self::PROPERTY_META_DATA, $other) &&
            $this->similarArrays($this->getMetaData(), $other[self::PROPERTY_META_DATA]);

        return $equal;
    }

    public function similarToImage(Image $other)
    {
        $equal = $this->getLocation() === $other->getLocation() &&
            $this->getTitle() === $other->getTitle() &&
            $this->getCaption() === $other->getCaption() &&
            $this->getCopyright() === $other->getCopyright() &&
            $this->getCopyrightUrl() === $other->getCopyrightUrl() &&
            $this->getSource() === $other->getSource() &&
            $this->similarArrays($this->getMetaData(), $other->getMetaData());

        return $equal;
    }

    public function __toString()
    {
        return $this->location;
    }

    protected function similarArrays(array $meta_data, array $other_meta_data)
    {
        $keys = array_keys($meta_data);
        $other_keys = array_keys($other_meta_data);

        if (count($keys) !== count($other_keys)) {
            return false; // different number of keys
        }

        foreach ($meta_data as $key => $value) {
            $is_equal = array_key_exists($key, $other_meta_data) && ($other_meta_data[$key] === $value);
            if (!$is_equal) {
                return false;
            }
        }

        return true;
    }
}
