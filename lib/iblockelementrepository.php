<?php

namespace Bx\Repository;

use BX\Data\Provider\IblockDataProvider;

class IblockElementRepository extends BaseBxRepository
{
    public function __construct(string $iblockType, string $iblockCode, string $modelClass)
    {
        $dataProvider = new IblockDataProvider($iblockType, $iblockCode);
        parent::__construct($dataProvider, $modelClass);
    }
}
