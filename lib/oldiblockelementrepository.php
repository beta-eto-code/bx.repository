<?php

namespace Bx\Repository;

use Access\Scope\Interfaces\AccessRecipientContextInterface;
use BX\Data\Provider\OldApiIblockDataProvider;
use Collection\Base\Interfaces\CollectionInterface;
use Data\Provider\Interfaces\CompareRuleInterface;
use Data\Provider\Interfaces\QueryCriteriaInterface;
use Repository\Base\Interfaces\FetcherInterface;
use Repository\Base\Interfaces\ModelFactoryInterface;

class OldIblockElementRepository extends BaseBxRepository
{
    private bool $usePermissions;

    public function __construct(
        string $iblockType,
        string $iblockCode,
        string $modelClass,
        bool $usePermissions = false
    ) {
        $this->usePermissions = $usePermissions;
        $dataProvider = OldApiIblockDataProvider::initByIblock($iblockType, $iblockCode);
        parent::__construct($dataProvider, $modelClass);
    }

    public function getCollection(
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null,
        string ...$fetchListNames
    ): CollectionInterface {
        $this->updateQuery($query, $recipientContext);
        return parent::getCollection($query, $recipientContext, ...$fetchListNames);
    }

    public function getModelCollection(
        ModelFactoryInterface $modelFactory,
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null,
        FetcherInterface ...$fetcherList
    ): CollectionInterface {
        $this->updateQuery($query, $recipientContext);
        return parent::getModelCollection($modelFactory, $query, $recipientContext, ...$fetcherList);
    }

    private function updateQuery(
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null
    ): void {
        if (!$this->usePermissions) {
            return;
        }

        $query->addCriteria('CHECK_PERMISSIONS', CompareRuleInterface::EQUAL, 'Y');
        if (empty($recipientContext)) {
            return;
        }

        $query->addCriteria(
            'PERMISSIONS_BY',
            CompareRuleInterface::EQUAL,
            $recipientContext->getRecipient()->getRecipientId()
        );
    }
}
