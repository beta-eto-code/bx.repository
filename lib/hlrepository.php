<?php

namespace Bx\Repository;

use BX\Data\Provider\HlBlockDataProvider;
use Data\Provider\Interfaces\DataProviderInterface;
use Exception;

class HlRepository extends BaseBxRepository
{
    /**
     * @throws Exception
     */
    public static function initById(int $hlBlockId, string $modelClass): HlRepository
    {
        $dataProvider = HlBlockDataProvider::initById($hlBlockId);
        return new HlRepository($dataProvider, $modelClass);
    }

    /**
     * @throws Exception
     */
    public static function initByHlName(string $hlBlockName, string $modelClass): HlRepository
    {
        $dataProvider = HlBlockDataProvider::initByHlName($hlBlockName);
        return new HlRepository($dataProvider, $modelClass);
    }

    /**
     * @throws Exception
     */
    public static function initByTableName(string $tableName, string $modelClass): HlRepository
    {
        $dataProvider = HlBlockDataProvider::initByTableName($tableName);
        return new HlRepository($dataProvider, $modelClass);
    }

    protected function __construct(DataProviderInterface $dataProvider, string $modelClass)
    {
        parent::__construct($dataProvider, $modelClass);
    }
}
