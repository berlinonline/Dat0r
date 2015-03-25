<?php

namespace Dat0r\Tests\Runtime\Attribute\Image;

use Dat0r\Runtime\Attribute\Image\Image;
use Dat0r\Tests\TestCase;
use InvalidArgumentException;

class ImageTest extends TestCase
{
    protected function setUp()
    {
        set_error_handler([$this, 'errorHandler']); // to catch missing argument and throw an exception for it
    }

    public function testSimpleCreateSucceeds()
    {
        $img = new Image('some/file.jpg');
        $this->assertEquals($img->getStorageLocation(), 'some/file.jpg');
    }

    /**
     * @expectedException Assert\InvalidArgumentException
     */
    public function testSimpleCreateFailsWithEmptyString()
    {
        $img = new Image('');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateWithoutArgumentsFails()
    {
        $img = new Image();
    }

    public function testComplexCreateSucceeds()
    {
        $img = new Image(
            'some/file.jpg',
            'title',
            'caption',
            'copyright',
            'copyright_url',
            'source',
            [
                'foo' => 'bar',
                'leet' => 1337
            ]
        );

        $this->assertEquals('some/file.jpg', $img->getStorageLocation());
        $this->assertEquals('title', $img->getTitle());
        $this->assertEquals('caption', $img->getCaption());
        $this->assertEquals('copyright', $img->getCopyright());
        $this->assertEquals('copyright_url', $img->getCopyrightUrl());
        $this->assertEquals('source', $img->getSource());
        $this->assertEquals(['foo' => 'bar', 'leet' => 1337], $img->getMetaData());
    }

    public function testCreateFromPartialArraySucceeds()
    {
        $img = Image::createFromArray([
            Image::PROPERTY_STORAGE_LOCATION => 'some/file.jpg',
            Image::PROPERTY_TITLE => 'title',
            Image::PROPERTY_SOURCE => 'source',
            Image::PROPERTY_META_DATA => [
                'foo' => 'bar',
                'leet' => 1337
            ]
        ]);

        $this->assertEquals('some/file.jpg', $img->getStorageLocation());
        $this->assertEquals('title', $img->getTitle());
        $this->assertEquals('source', $img->getSource());
        $this->assertEquals(['foo' => 'bar', 'leet' => 1337], $img->getMetaData());
    }

    public function testCreateFromOtherImageSucceeds()
    {
        $other_img = new Image('some/other.png', 'other_title');

        $img = Image::createFromImage($other_img);

        $this->assertEquals('some/other.png', $img->getStorageLocation());
        $this->assertEquals('other_title', $img->getTitle());
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        throw new InvalidArgumentException(
            sprintf(
                'Missing argument. %s %s %s %s',
                $errno,
                $errstr,
                $errfile,
                $errline
            )
        );
    }
}
