<?php

namespace Bx\Repository;

use BX\Data\Provider\DataManagerDataProvider;
use Exception;
use Bitrix\Main\ORM\Data\DataManager;

class DataManagerRepository extends BaseBxRepository
{
    public function __construct(string $dataManagerClass, string $modelClass)
    {
        if (!class_exists($dataManagerClass)) {
            throw new Exception("Class {$dataManagerClass} not found");
        }

        if (!is_a($dataManagerClass, DataManager::class, true)) {
            throw new Exception("Class {$dataManagerClass} must be an instance of DataManager");
        }

        $dataProvider = new DataManagerDataProvider($dataManagerClass);
        parent::__construct($dataProvider, $modelClass);
    }
}
