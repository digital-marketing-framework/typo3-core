<?php

namespace DigitalMarketingFramework\Typo3\Core\Utility;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use TYPO3\CMS\Core\Core\Environment;

class VendorAssetUtility
{
    public const PATH_VENDOR = 'vendor';
    public const PATH_ASSETS = 'typo3temp/assets';

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

    protected static function getSourcePath(string $path): string
    {
        return static::getVendorPath() . '/' . $path;
    }

    protected static function getRelativeTargetPath(string $path): string
    {
        // vendor-name/package-name/assets/scripts/my-script.js
        // ... becomes...
        // vendor-name/package-name/scripts/my-script.js
        // ... because the "assets" folder is already included in the temp directory
        $pathParts = explode('/', $path);
        if ($pathParts[2] === 'assets') {
            array_splice($pathParts, 2, 1);
            $path = implode('/', $pathParts);
        }
        return $path;
    }

    protected static function getTargetPath(string $path): string
    {
        return static::getTempPath() . '/' . static::getRelativeTargetPath($path);
    }

    protected static function getPublicTargetPath(string $path): string
    {
        return static::getPublicTempPath() . '/' . static::getRelativeTargetPath($path);
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
            mkdir($folder, recursive:true);
        }
    }

    protected static function copyFile(string $path): void
    {
        $source = static::getSourcePath($path);
        $target = static::getTargetPath($path);

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

    protected static function getPublicUrl(string $path): string
    {
        $source = static::getSourcePath($path);
        $url = static::getPublicTargetPath($path);
        $hash = static::getCacheHash($source);
        if ($hash !== '') {
            $url .= '?' . $hash;
        }
        return $url;
    }

    public static function makeVendorAssetAvailable(string $path): string
    {
        $path = ltrim($path, '/');
        static::copyFile($path);
        return static::getPublicUrl($path);
    }
}
