<?php

namespace Bx\Repository;

use BX\Data\Provider\SectionIblockDataProvider;

class IblockSectionRepository extends BaseBxRepository
{
    public function __construct(string $iblockType, string $iblockCode, string $modelClass)
    {
        $dataProvider = new SectionIblockDataProvider($iblockType, $iblockCode);
        parent::__construct($dataProvider, $modelClass);
    }
}
