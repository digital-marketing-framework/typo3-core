<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\JsonResponse;
use DigitalMarketingFramework\Core\Backend\Response\RedirectResponse;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\HtmlResponse as Typo3HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse as Typo3JsonResponse;
use TYPO3\CMS\Core\Http\RedirectResponse as Typo3RedirectResponse;

/*
 * routes
 * - page.dashboard.index
 * - page.distributor.overview
 * - page.distributor.error.list [filters,navigation]
 * - page.distributor.job.list [filters,navigation]
 * - page.distributor.job.edit [job-id,return-url]
 * - page.distributor.job.preview [job-id-list,return-url]
 * - page.distributor.job.run [job-id-list]
 * - page.distributor.job.queue [job-id-list]
 * - page.distributor.job.delete [job-id-list]
 * - page.distributor.job.test-case.create [job-id]
 * - page.distributor.job.test-case.update [job-id,test-case-id]
 * - page.test-case.list [filters,navigation]
 * - page.test-case.run [test-case-id-list]
 * - page.test-case.edit [test-case-id,return-url]
 * - page.test-case.delete [test-case-id]
 * - page.configuration-document.list
 * - page.configuration-document.edit [configuration-document-id]
 * - page.configuration-document.delete [configuration-document-id]
 * - page.global-settings.edit
 * - page.notification.list [filters,navigation]
 * - page.notification.edit [notification-id]
 * - page.notification.delete [notification-id]
 * - page.notification.test [title,message,component,level]
 * - ajax.configuration-document.schema [domain]
 * - ajax.configuration-document.defaults [domain]
 * - ajax.configuration-document.merge [domain,document,parent]
 * - ajax.configuration-document.split [domain,document]
 * - ajax.configuration-document.update-includes [domain,document]
 */

class BackendModuleController
{
    public function __construct(
        protected RegistryCollection $registryCollection,
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    protected function getBodyData(ServerRequestInterface $request): array
    {
        // for POST form submissions
        $body = $request->getParsedBody();

        if ($body === null) {
            try {
                // for POST AJAX requests with a JSON body
                $body = json_decode($request->getBody(), true, flags: JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                $body = [];
            }
        }

        if (!is_array($body)) {
            $body = [];
        }

        return $body;
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams()['dmf'] ?? [];
        $route = $params['route'] ?? '';
        $arguments = $params['arguments'] ?? [];
        $body = $this->getBodyData($request);
        $method = $request->getMethod();

        $req = new Request($route, $arguments, $body, $method);
        $result = $this->registryCollection->getRegistry()->getBackendManager()->getResponse($req);

        if ($result instanceof RedirectResponse) {
            return new Typo3RedirectResponse($result->getRedirectLocation());
        } elseif ($result instanceof JsonResponse) {
            return new Typo3JsonResponse($result->getData());
        }

        return new Typo3HtmlResponse($result->getContent());
    }
}
