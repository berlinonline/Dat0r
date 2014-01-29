<?php

namespace Dat0r\CodeGen\ClassBuilder;

use Dat0r\Common\Object;
use Dat0r\Common\Error\NotReadableException;
use Dat0r\Common\Error\NotWritableException;
use Symfony\Component\Filesystem\Filesystem;

class BuildCache extends Object
{
    const DIR_MODE = 0750;

    const FILE_MODE = 0750;

    protected $cache_directory;

    protected $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * @throws Symfony\Component\Filesystem\Exception\IOExceptionInterface
     */
    public function purge()
    {
        if (is_dir($this->cache_directory)) {
            $this->filesystem->remove($this->cache_directory);
        }
    }

    /**
     * @throws Symfony\Component\Filesystem\Exception\IOExceptionInterface
     */
    public function generate(ClassContainerList $class_containers)
    {
        $this->purge();

        if (!is_dir($this->cache_directory)) {
            $this->filesystem->mkdir($this->cache_directory, self::DIR_MODE);
        }

        foreach ($class_containers as $class_container) {
            $relative_path = str_replace('\\', DIRECTORY_SEPARATOR, $class_container->getPackage());
            $package_dir = $this->cache_directory . DIRECTORY_SEPARATOR . $relative_path;

            if (!is_dir($package_dir)) {
                $this->filesystem->mkdir($package_dir, self::DIR_MODE);
            }

            $class_filepath = $package_dir . DIRECTORY_SEPARATOR . $class_container->getFileName();
            $this->filesystem->dumpFile(
                $class_filepath,
                $class_container->getSourceCode(),
                self::FILE_MODE
            );
        }
    }

    /**
     * @throws Symfony\Component\Filesystem\Exception\IOExceptionInterface
     * @throws Dat0r\Common\Error\NotReadableException
     */
    public function deploy($deploy_directory, $method = 'move')
    {
        if (!is_dir($this->cache_directory) || !is_readable($this->cache_directory)) {
            throw new NotReadableException(
                sprintf(
                    "The cache directory '%s' does not exist or isn't readable.",
                    $this->cache_directory
                )
            );
        }

        if (!is_dir($deploy_directory)) {
            $this->filesystem->mkdir($deploy_directory, self::DIR_MODE);
        }
        if (!is_writable($deploy_directory)) {
            throw new NotWritableException(
                sprintf(
                    "The deploy directory '%s' isn't writeable. Permissions?",
                    $deploy_directory
                )
            );
        }

        if ('move' === $method) {
            $this->filesystem->rename($this->cache_directory, $deploy_directory, true);
        } else {
            $this->filesystem->mirror($this->cache_directory, $deploy_directory, null, array('override' => true));
        }
    }
}
