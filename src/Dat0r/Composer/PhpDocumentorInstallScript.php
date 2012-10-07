<?php

namespace Dat0r\Composer;

use Composer\Script\Event;

/**
 * This class works around the phpDocumentor/UnifiedAssetInstaller's way of defining the data directory
 * that it installs templates and other phpdocumentor assets to.
 *
 * As you can tell from the following link, the path is hard coded to be relative to the current working directory.
 * https://github.com/phpDocumentor/UnifiedAssetInstaller/blob/master/src/phpDocumentor/Composer/UnifiedAssetInstaller.php#L21, 
 * 
 * This class is registered to composer's post-install-cmd and post-update-cmd hooks 
 * and moves all phpdoc data from the project's root to phpdoc's vendor directory.
 * 
 * @codeCoverageIgnore
 */
class PhpDocumentorInstallScript
{
    /**
     * Moves phpdocumentor assets from a project's root dir to the phpdocumentor vendor directory
     * and deletes the data directory from the project's root dir.
     *
     * @param Event $event
     */
    public static function fixAssets(Event $event)
    {
        try
        {
            echo "Moving phpDocumentor assets from project root to phpDocumentor's vendor directory." . PHP_EOL;
            $projectRoot = self::locateProjectRoot();

            $vendorDir = $projectRoot . DIRECTORY_SEPARATOR . 'vendor';
            $dislikedPhpDocDataDir = $projectRoot . DIRECTORY_SEPARATOR . 'data';

            $vendorPathParts = array('phpdocumentor', 'phpdocumentor', 'data');
            $expectedPhpDocDataDir = $vendorDir . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $vendorPathParts);

            $fromDirectory = $dislikedPhpDocDataDir . DIRECTORY_SEPARATOR . 'templates';
            $toDirectory = $expectedPhpDocDataDir . DIRECTORY_SEPARATOR . 'templates';

            self::moveAssets($fromDirectory, $toDirectory);

            rmdir($fromDirectory);
            rmdir($dislikedPhpDocDataDir);
        }
        catch (Exception $e)
        {
            printf('An error occured while processing composer post-install hook in "%s".', __CLASS__);
            echo PHP_EOL . $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Tries to resolve our project base directory,
     * meaning one directory above the vendor directory we've been installed to.
     *
     * @return string
     */
    protected static function locateProjectRoot()
    {
        $baseDir = dirname(dirname(dirname(__DIR__)));
        if (! is_dir($baseDir . DIRECTORY_SEPARATOR . 'vendor'))
        {
            $baseDir = dirname(dirname(dirname($baseDir)));
        }
        if (! is_dir($baseDir . DIRECTORY_SEPARATOR . 'vendor'))
        {
            throw new Exception('Unable to locate vendor directory.');
        }
        return $baseDir;
    }

    /**
     * Moves the phpdocumentor data from the root to the vendor directory.
     *
     * @param string $from
     * @param string $to
     */
    protected static function moveAssets($from, $to)
    {
        if (! is_writable($from) || ! is_writable($to))
        {
            throw new Exception(
                "Error, while trying to move phpdocumentor's assets/templates." . PHP_EOL .
                'One of either the given source or target directories is not writeable.'
            );
        }

        foreach (glob($from . DIRECTORY_SEPARATOR . '*') as $assetDirectory)
        {
            $assetName = basename($assetDirectory);
            $targetPath = $to . DIRECTORY_SEPARATOR . $assetName;
            if (is_dir($targetPath))
            {
                self::removeDirectory($assetDirectory);
                echo "- Asset/template '$assetName' allready exists inside vendor directory." . PHP_EOL;
            }
            else
            {
                rename($assetDirectory, $targetPath);
                echo "- Moved phpdocumentor asset/template: $assetName." . PHP_EOL;
            }
        }
    }

    /**
     * Removes the given directory.
     *
     * @param string $directory
     */
    protected static function removeDirectory($directory)
    {
        $dirHandle = opendir($directory);
        while (($file = readdir($dirHandle)) !== FALSE)
        {
            if ('.' === $file || '..' === $file)
            {
                continue;
            }
            $path = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path))
            {
                self::removeDirectory($path);
            }
            else
            {
                unlink($path);
            }
        }
        closedir($dirHandle);
        rmdir($directory);
    }
}
