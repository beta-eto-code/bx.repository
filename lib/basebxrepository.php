<?php

namespace Bx\Repository;

use Access\Scope\Interfaces\AccessRecipientContextInterface;
use Bx\Repository\Interfaces\FetchableInterface;
use Bx\Repository\Interfaces\SavableInterface;
use Bx\Repository\Interfaces\SelectableInterface;
use Collection\Base\Interfaces\CollectionInterface;
use Collection\Base\Interfaces\CollectionItemInterface;
use Data\Provider\Interfaces\DataProviderInterface;
use Data\Provider\Interfaces\PkOperationResultInterface;
use Data\Provider\Interfaces\QueryCriteriaInterface;
use Data\Provider\OperationResult;
use Exception;
use Repository\Base\BaseRepository;

class BaseBxRepository extends BaseRepository
{
    private string $modelClass;
    private ?bool $supportSaveOperation = null;
    private ?bool $modelHasSelectFields = null;
    private ?array $fetchList = null;

    public function __construct(DataProviderInterface $dataProvider, string $modelClass)
    {
        $this->modelClass = $modelClass;
        parent::__construct($dataProvider);
    }

    protected function getClassModel(): string
    {
        return $this->modelClass;
    }

    /**
     * @throws Exception
     */
    public function initWithModelClass(string $modelClass): BaseRepository
    {
        $repository = clone $this;
        $repository->modelClass = $modelClass;
        return $repository;
    }

    public function getCollection(
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null,
        string ...$fetchListNames
    ): CollectionInterface {
        if (empty($query->getSelect()) && $this->isModelHasSelectFields()) {
            $query->setSelect($this->modelClass::getSelectFiledNames());
        }

        return parent::getCollection($query, $recipientContext, ...$fetchListNames);
    }

    protected function getFetcherList(): array
    {
        if (is_null($this->fetchList)) {
            $modelSupportFetch = is_a($this->modelClass, FetchableInterface::class, true);
            $this->fetchList = $modelSupportFetch ? $this->modelClass::getFetchList() : [];
        }

        return $this->fetchList;
    }

    public function save(
        CollectionItemInterface $item,
        ?AccessRecipientContextInterface $recipientContext = null
    ): PkOperationResultInterface
    {
        if (!$this->isModelSupportSaveOperation()) {
            return new OperationResult($this->modelClass . ' is not support save operation');
        }
        return $item->save($this->dataProvider, $recipientContext);
    }

    private function isModelSupportSaveOperation(): bool
    {
        if (is_null($this->supportSaveOperation)) {
            $this->supportSaveOperation = is_a($this->modelClass, SavableInterface::class, true);
        }
        return $this->supportSaveOperation;
    }

    private function isModelHasSelectFields(): bool
    {
        if (is_null($this->modelHasSelectFields)) {
            $this->modelHasSelectFields = is_a($this->modelClass, SelectableInterface::class, true);
        }
        return $this->modelHasSelectFields;
    }
}
