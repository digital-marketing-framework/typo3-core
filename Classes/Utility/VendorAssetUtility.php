<?php

namespace DigitalMarketingFramework\Typo3\Core\Utility;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use TYPO3\CMS\Core\Core\Environment;

class VendorAssetUtility
{
    /**
     * @var string
     */
    public const PATH_VENDOR = 'vendor';

    /**
     * @var string
     */
    public const PATH_ASSETS = 'typo3temp/assets/vendor-assets';

    protected static function getVendorPath(): string
    {
        return Environment::getProjectPath() . '/' . static::PATH_VENDOR;
    }

    protected static function getTempPath(): string
    {
        return Environment::getPublicPath() . '/' . static::PATH_ASSETS;
    }

    protected static function getPublicTempPath(): string
    {
        return static::PATH_ASSETS;
    }

    protected static function getSourcePath(string $composerName, string $path): string
    {
        return static::getVendorPath() . '/' . $composerName . '/assets/' . $path;
    }

    protected static function getRelativeTargetPath(string $composerName, string $path): string
    {
        $pathParts = explode('/', $path);
        $lastPathPart = array_pop($pathParts);
        $leadingPath = implode('/', $pathParts);
        $relativePath = $composerName . '/' . $leadingPath;
        $salt = $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'];

        return strrev(md5($relativePath . '|' . $salt)) . '/' . $lastPathPart;
    }

    protected static function getTargetPath(string $composerName, string $path): string
    {
        return static::getTempPath() . '/' . static::getRelativeTargetPath($composerName, $path);
    }

    protected static function getPublicTargetPath(string $composerName, string $path): string
    {
        return static::getPublicTempPath() . '/' . static::getRelativeTargetPath($composerName, $path);
    }

    protected static function getCacheHash(string $source): string
    {
        return strrev(hash_file('md5', $source));
    }

    protected static function updateTargetFolder(string $target): void
    {
        $pathInfo = pathinfo($target);
        $folder = $pathInfo['dirname'];
        if (!is_dir($folder)) {
            if (file_exists($folder)) {
                throw new DigitalMarketingFrameworkException(sprintf('Asset target folder "%s" seems to be a file.', $folder));
            }

            mkdir($folder, recursive: true);
        }
    }

    protected static function copyFile(string $composerName, string $path): void
    {
        $source = static::getSourcePath($composerName, $path);
        $target = static::getTargetPath($composerName, $path);

        if (!file_exists($source)) {
            throw new DigitalMarketingFrameworkException(sprintf('Asset "%s" does not seem to exist.', $source));
        }

        if (file_exists($target)) {
            if (static::getCacheHash($source) !== static::getCacheHash($target)) {
                unlink($target);
                copy($source, $target);
            }
        } else {
            static::updateTargetFolder($target);
            copy($source, $target);
        }
    }

    protected static function getPublicUrl(string $composerName, string $path): string
    {
        $source = static::getSourcePath($composerName, $path);
        $url = static::getPublicTargetPath($composerName, $path);
        $hash = static::getCacheHash($source);
        if ($hash !== '') {
            $url .= '?' . $hash;
        }

        return $url;
    }

    protected static function checkFile(string $composerName, string $path): void
    {
        if (!preg_match('/^[-_a-zA-Z0-9]+\\/[-_a-zA-Z0-9]+$/', $composerName)) {
            throw new DigitalMarketingFrameworkException(sprintf('composer name "%s" is invalid', $composerName));
        }

        $source = realpath(static::getSourcePath($composerName, $path));
        if ($source === false) {
            throw new DigitalMarketingFrameworkException(sprintf('source "%s" file does not seem to exist in package "%s"', $path, $composerName));
        }

        $sourceFolder = realpath(static::getSourcePath($composerName, ''));
        if (!str_starts_with($source, $sourceFolder)) {
            throw new DigitalMarketingFrameworkException(sprintf('asset path "%s" seems to lead out of package assets folder', $path));
        }
    }

    public static function makeVendorAssetAvailable(string $composerName, string $path): string
    {
        $path = ltrim($path, '/');
        static::checkFile($composerName, $path);
        static::copyFile($composerName, $path);

        return static::getPublicUrl($composerName, $path);
    }
}
