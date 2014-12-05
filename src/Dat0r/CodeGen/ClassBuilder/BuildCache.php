<?php

namespace Dat0r\CodeGen\ClassBuilder;

use Dat0r\Common\Object;
use Dat0r\Common\Error\NotReadableException;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\Error\NotWritableException;
use Symfony\Component\Filesystem\Filesystem;

class BuildCache extends Object
{
    const CHECKSUM_FILE = '.checksum';

    const DIR_MODE = 0750;

    const FILE_MODE = 0750;

    protected $cache_directory;

    protected $deploy_directory;

    protected $file_system;

    public function __construct(array $state = array())
    {
        $this->file_system = new Filesystem();

        parent::__construct($state);
    }

    /**
     * @throws Symfony\Component\Filesystem\Exception\IOExceptionInterface
     */
    public function purge()
    {
        if (is_dir($this->cache_directory)) {
            $this->file_system->remove($this->cache_directory);
        }
    }

    /**
     * @throws Symfony\Component\Filesystem\Exception\IOExceptionInterface
     * @throws Dat0r\Common\Error\NotWritableException
     */
    public function generate(ClassContainerList $class_containers)
    {
        $this->purge();

        if (!is_dir($this->cache_directory)) {
            $this->file_system->mkdir($this->cache_directory, self::DIR_MODE);
        }
        if (!is_writable($this->cache_directory)) {
            throw new NotWritableException(
                sprintf("The cache directory '%s' isn't writeable. Permissions?", $this->cache_directory)
            );
        }

        $this->generateFiles($class_containers);
    }

    /**
     * @throws Symfony\Component\Filesystem\Exception\IOExceptionInterface
     * @throws Dat0r\Common\Error\NotReadableException
     * @throws Dat0r\Common\Error\NotWritableException
     */
    public function deploy(ClassContainerList $class_containers, $method = 'move')
    {
        $this->validateSetup($class_containers);

        if (!is_dir($this->deploy_directory)) {
            $this->file_system->mkdir($this->deploy_directory, self::DIR_MODE);
        }
        if (!is_writable($this->deploy_directory)) {
            throw new NotWritableException(
                sprintf("The deploy directory '%s' isn't writeable. Permissions?", $this->deploy_directory)
            );
        }

        $this->deployFiles($class_containers, $method);
    }

    protected function generateFiles(ClassContainerList $class_containers)
    {
        $checksum = '';
        foreach ($class_containers as $class_container) {
            $relative_path = str_replace('\\', DIRECTORY_SEPARATOR, $class_container->getPackage());
            $package_dir = $this->cache_directory . DIRECTORY_SEPARATOR . $relative_path;

            if (!is_dir($package_dir)) {
                $this->file_system->mkdir($package_dir, self::DIR_MODE);
            }

            $class_filepath = $package_dir . DIRECTORY_SEPARATOR . $class_container->getFileName();
            $this->file_system->dumpFile($class_filepath, $class_container->getSourceCode(), self::FILE_MODE);
            $checksum .= md5_file($class_filepath);
        }
        $checksum_file = $this->cache_directory . DIRECTORY_SEPARATOR . self::CHECKSUM_FILE;
        $this->file_system->dumpFile($checksum_file, md5($checksum), self::FILE_MODE);
    }

    protected function deployFiles(ClassContainerList $class_containers, $method = 'move')
    {
        foreach ($class_containers as $class_container) {
            $relative_path = str_replace('\\', DIRECTORY_SEPARATOR, $class_container->getPackage());
            $cache_package_dir = $this->cache_directory . DIRECTORY_SEPARATOR . $relative_path;
            $cache_filepath = $cache_package_dir . DIRECTORY_SEPARATOR . $class_container->getFileName();
            $deploy_package_dir = $this->deploy_directory . DIRECTORY_SEPARATOR . $relative_path;
            $deploy_filepath = $deploy_package_dir . DIRECTORY_SEPARATOR . $class_container->getFileName();

            if (!is_dir($deploy_package_dir)) {
                $this->file_system->mkdir($deploy_package_dir, self::DIR_MODE);
            }

            $package_parts = explode('\\', $class_container->getPackage());
            $override = false;
            if ('Base' === end($package_parts)) {
                $override = true;
            }

            if (!file_exists($deploy_filepath) || $override) {
                if ('move' === $method) {
                    $this->file_system->rename($cache_filepath, $deploy_filepath, true);
                } else {
                    $this->file_system->copy($cache_filepath, $deploy_filepath, true);
                }
            }
        }
    }

    protected function validateSetup(ClassContainerList $class_containers)
    {
        if (!is_dir($this->cache_directory) || !is_readable($this->cache_directory)) {
            throw new NotReadableException(
                sprintf("The cache directory '%s' does not exist or isn't readable.", $this->cache_directory)
            );
        }

        $checksum_file = $this->cache_directory . DIRECTORY_SEPARATOR . self::CHECKSUM_FILE;
        if (!is_readable($checksum_file)) {
            throw new NotReadableException(
                sprintf("The cache-checksum file '%s' does not exist or isn't readable.", $checksum_file)
            );
        }

        $challenge = file_get_contents($checksum_file);
        if ($this->generateChecksum($class_containers) !== $challenge) {
            throw new RuntimeException(
                sprintf(
                    "Cache checksum didn't match. The generated code within '%s' was modified. " .
                    "Regenerate the type's schema code and then deploy again.",
                    $this->cache_directory
                )
            );
        }
    }

    protected function generateChecksum(ClassContainerList $class_containers)
    {
        $checksum = '';
        foreach ($class_containers as $class_container) {
            $relative_path = str_replace('\\', DIRECTORY_SEPARATOR, $class_container->getPackage());
            $package_dir = $this->cache_directory . DIRECTORY_SEPARATOR . $relative_path;
            $class_filepath = $package_dir . DIRECTORY_SEPARATOR . $class_container->getFileName();
            $checksum .= md5_file($class_filepath);
        }

        return md5($checksum);
    }
}
