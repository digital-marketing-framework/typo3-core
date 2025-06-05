<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Repository\Api;

use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Typo3\Core\Domain\Model\Api\EndPoint;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<EndPoint>
 */
class EndPointRepository extends Repository implements EndPointStorageInterface
{
    protected int $pid;

    public function __construct(
        protected ExtensionConfiguration $extensionConfiguration,
    ) {
        $typo3Version = new Typo3Version();
        if ($typo3Version->getMajorVersion() <= 11) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class); // @phpstan-ignore-line TYPO3 version switch
            parent::__construct($objectManager); // @phpstan-ignore-line TYPO3 version switch
        } else {
            parent::__construct(); // @phpstan-ignore-line TYPO3 version switch
        }
    }

    protected function getPid(): int
    {
        if (!isset($this->pid)) {
            try {
                $this->pid = $this->extensionConfiguration->get('dmf_core')['api']['pid'] ?? 0;
            } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
                $this->pid = 0;
            }
        }

        return $this->pid;
    }

    public function getEndPointsFiltered(array $navigation): array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(true);
        $query->getQuerySettings()->setStoragePageIds([$this->getPid()]);

        if ($navigation['itemsPerPage'] > 0) {
            $query->setLimit($navigation['itemsPerPage']);
            if ($navigation['page'] > 0) {
                $query->setOffset($navigation['itemsPerPage'] * $navigation['page']);
            }
        }

        return $query->execute()->toArray();
    }

    public function getAllEndPoints(): array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(true);
        $query->getQuerySettings()->setStoragePageIds([$this->getPid()]);

        return $query->execute()->toArray();
    }

    public function getEndPointCount(): int
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(true);
        $query->getQuerySettings()->setStoragePageIds([$this->getPid()]);

        return $query->count();
    }

    public function getEndPointByName(string $name): ?EndPointInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(true);
        $query->getQuerySettings()->setStoragePageIds([$this->getPid()]);

        $query->matching($query->equals('name', $name));
        $query->setLimit(1);

        $result = $query->execute()->toArray();

        return $result[0] ?? null;
    }

    public function fetchByIdList(array $ids): array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->in('uid', $ids));

        return $query->execute()->toArray();
    }

    public function createEndPoint(string $name): EndPointInterface
    {
        return new EndPoint($name);
    }

    public function addEndPoint(EndPointInterface $endPoint): void
    {
        if (!$endPoint instanceof EndPoint) {
            throw new DigitalMarketingFrameworkException(sprintf('Unknown type of API end point record "%s".', $endPoint::class), 2932987763);
        }

        $endPoint->setPid($this->getPid());
        $this->add($endPoint);
        $this->persistenceManager->persistAll();
    }

    public function removeEndPoint(EndPointInterface $endPoint): void
    {
        if (!$endPoint instanceof EndPoint) {
            throw new DigitalMarketingFrameworkException(sprintf('Unknown type of API end point record "%s".', $endPoint::class), 3531287185);
        }

        $this->remove($endPoint);
        $this->persistenceManager->persistAll();
    }

    public function updateEndPoint(EndPointInterface $endPoint): void
    {
        if (!$endPoint instanceof EndPoint) {
            throw new DigitalMarketingFrameworkException(sprintf('Unknown type of API end point record "%s".', $endPoint::class), 3516553498);
        }

        $this->update($endPoint);
    }
}
