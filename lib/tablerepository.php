<?php

namespace Bx\Repository;

use BX\Data\Provider\BxConnectionDataProvider;
use Data\Provider\SqlBuilderMySql;

class TableRepository extends BaseBxRepository
{
    public function __construct(string $tableName, string $modelClass, ?string $pkName, ?string $connectionName = null)
    {
        $dataProvider = new BxConnectionDataProvider(new SqlBuilderMySql(), $tableName, $connectionName, $pkName);
        parent::__construct($dataProvider, $modelClass);
    }
}
