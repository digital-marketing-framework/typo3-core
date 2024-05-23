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

    protected static function copyFile(string $composerName, string $path, array $replacements = []): void
    {
        $source = static::getSourcePath($composerName, $path);
        $target = static::getTargetPath($composerName, $path);

        if (!file_exists($source)) {
            throw new DigitalMarketingFrameworkException(sprintf('Asset "%s" does not seem to exist.', $source));
        }

        $copy = false;
        if (file_exists($target)) {
            if (static::getCacheHash($source) !== static::getCacheHash($target)) {
                unlink($target);
                $copy = true;
            }
        } else {
            static::updateTargetFolder($target);
            $copy = true;
        }

        if ($copy) {
            copy($source, $target);

            // TODO updating paths in the target file will change its cache hash
            //      which will cause the file to be copied every time
            //      even if the contents did not change
            if ($replacements !== []) {
                $contents = file_get_contents($target);
                foreach ($replacements as $searchBasePath => $replacementBasePath) {
                    $searchBasePath = preg_quote($searchBasePath, '/');
                    $contents = preg_replace_callback('/"' . $searchBasePath . '([^"]+)"/', function($match) use ($composerName, $replacementBasePath) {
                        $relativePath = $replacementBasePath . $match[1];
                        return '"/' . static::makeVendorAssetAvailable($composerName, $relativePath) . '"';
                    }, $contents);
                }
                file_put_contents($target, $contents);
            }
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

    public static function makeVendorAssetAvailable(string $composerName, string $path, array $replacements = []): string
    {
        $path = ltrim($path, '/');
        static::checkFile($composerName, $path);
        static::copyFile($composerName, $path, $replacements);

        return static::getPublicUrl($composerName, $path);
    }
}
