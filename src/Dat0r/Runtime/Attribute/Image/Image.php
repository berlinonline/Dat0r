<?php

namespace Dat0r\Runtime\Attribute\Image;

class Image
{
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
        $this->storage_location = $img->getStorageLocation();
        $this->title = $img->getTitle();
        $this->caption = $img->getCaption();
        $this->copyright = $img->getCopyright();
        $this->copyright_url = $img->getCopyrightUrl();
        $this->source = $img->getSource();
        $this->meta_data = $img->getMetaData();
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
