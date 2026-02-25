<?php

namespace DigitalMarketingFramework\Typo3\Core\Utility;

use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;

/**
 * Workaround for TYPO3 13+ CLI commands that need Extbase configuration.
 *
 * Extbase's ConfigurationManager requires a ServerRequest to resolve TypoScript
 * settings. In CLI context no request exists, which throws
 * NoServerRequestGivenException. This utility temporarily sets a fake backend
 * request in $GLOBALS['TYPO3_REQUEST'] so the ConfigurationManager can function.
 *
 * TYPO3 core uses the same pattern in its own CLI commands
 * (e.g. DeactivateExtensionCommand, ResetPasswordCommand).
 *
 * Note: Commands using this utility with FAL operations also need
 * Bootstrap::initializeBackendAuthentication() to avoid permission errors
 * from StoragePermissionsAspect (which checks isAdmin() on the BE_USER).
 *
 * @see \TYPO3\CMS\Extbase\Configuration\ConfigurationManager::getConfiguration()
 * @see \TYPO3\CMS\Core\Resource\Security\StoragePermissionsAspect
 */
class CliEnvironmentUtility
{
    /**
     * Execute a callback with a backend ServerRequest guaranteed to exist.
     *
     * If $GLOBALS['TYPO3_REQUEST'] is already set, the callback runs as-is.
     * Otherwise a minimal fake backend request is created, the callback is
     * executed, and the fake request is cleaned up — even if the callback throws.
     *
     * @template T
     *
     * @param callable(): T $callback
     *
     * @return T
     */
    public static function ensureBackendRequest(callable $callback): mixed
    {
        if (isset($GLOBALS['TYPO3_REQUEST'])) {
            return $callback();
        }

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
        try {
            return $callback();
        } finally {
            unset($GLOBALS['TYPO3_REQUEST']);
        }
    }
}
