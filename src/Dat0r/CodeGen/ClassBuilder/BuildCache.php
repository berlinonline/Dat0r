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

    protected $deploy_directory;

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
     * @throws Dat0r\Common\Error\NotWritableException
     */
    public function generate(ClassContainerList $class_containers)
    {
        $this->purge();

        if (!is_dir($this->cache_directory)) {
            $this->filesystem->mkdir($this->cache_directory, self::DIR_MODE);
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
            $this->filesystem->mkdir($this->deploy_directory, self::DIR_MODE);
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
                $this->filesystem->mkdir($package_dir, self::DIR_MODE);
            }

            $class_filepath = $package_dir . DIRECTORY_SEPARATOR . $class_container->getFileName();
            $this->filesystem->dumpFile($class_filepath, $class_container->getSourceCode(), self::FILE_MODE);
            $checksum .= md5_file($class_filepath);
        }
        $checksum_file = $this->cache_directory . DIRECTORY_SEPARATOR . 'cache.md5';
        $this->filesystem->dumpFile($checksum_file, md5($checksum), self::FILE_MODE);
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
                $this->filesystem->mkdir($deploy_package_dir, self::DIR_MODE);
            }

            $package_parts = explode('\\', $class_container->getPackage());
            $override = ('Base' === end($package_parts));
            if ('move' === $method) {
                $this->filesystem->rename($cache_filepath, $deploy_filepath, $override);
            } else {
                $this->filesystem->copy($cache_filepath, $deploy_filepath, null, array('override' => $override));
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

        $checksum_file = $this->cache_directory . DIRECTORY_SEPARATOR . 'cache.md5';
        if (!is_readable($checksum_file)) {
            throw new NotReadableException(
                sprintf("The cache-checksum file '%s' does not exist or isn't readable.", $checksum_file)
            );
        }

        $challenge = file_get_contents($checksum_file);
        if ($this->generateChecksum($class_containers) !== $challenge) {
            throw new RuntimeException(
                "The cache checksum is corrupt, meaning that the generated code was modified. " .
                "Regenerate the module schema's code and then deploy again."
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
