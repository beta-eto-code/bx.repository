<?php

namespace Bx\Repository\DataProviders;

use ArrayObject;
use BX\Data\Provider\BxQueryAdapter;
use BX\Data\Provider\DataManagerDataProvider;
use Data\Provider\Interfaces\OperationResultInterface;
use Data\Provider\Interfaces\PkOperationResultInterface;
use Data\Provider\Interfaces\QueryCriteriaInterface;
use Data\Provider\OperationResult;
use Bitrix\Main\UserTable;
use CUser;

class UserDataProvider extends DataManagerDataProvider
{
    public function __construct()
    {
        parent::__construct(UserTable::class, 'ID');
    }

    protected function saveInternal(&$data, QueryCriteriaInterface $query = null): PkOperationResultInterface
    {
        if (empty($query)) {
            $dataResult = ['data' => $data];
            $dataForSave = $data instanceof ArrayObject ? iterator_to_array($data) : $data;
            $cUser = new CUser();
            $id = (int) $cUser->Add($dataForSave);
            if ($id > 0) {
                $data['ID'] = $id;
                return new OperationResult(null, $dataResult, $id);
            }

            return new OperationResult(
                "Ошибка добавления пользователя: {$cUser->LAST_ERROR}",
                $dataResult
            );
        }

        $dataResult = ['query' => $query, 'data' => $data];
        $errorMessage = 'Данные для обновления не найдены';
        $pkName = $this->getPkName();
        if (empty($pkName)) {
            return new OperationResult(
                $errorMessage,
                $dataResult
            );
        }

        $bxQuery = BxQueryAdapter::init($query);
        $pkListForUpdate = $this->getPkValuesByQuery($bxQuery);
        if (empty($pkListForUpdate)) {
            return new OperationResult(
                $errorMessage,
                $dataResult
            );
        }

        $mainResult = null;
        $cUser = new CUser();
        foreach ($pkListForUpdate as $pkValue) {
            $dataForSave = $data instanceof ArrayObject ? iterator_to_array($data) : $data;
            $isSuccess = (bool)$cUser->Update($pkValue, $dataForSave);
            $updateResult = $isSuccess ?
                new OperationResult('', $dataResult, $pkValue) :
                new OperationResult(
                    "Ошибка обновления пользователя: {$cUser->LAST_ERROR}",
                    $dataResult,
                    $pkValue
                );

            if ($mainResult instanceof OperationResultInterface) {
                $mainResult->addNext($updateResult);
            } else {
                $mainResult = $updateResult;
            }
        }

        return $mainResult instanceof PkOperationResultInterface ?
            $mainResult :
            new OperationResult('Данные для сохранения не найдены', $dataResult);
    }

    public function remove(QueryCriteriaInterface $query): OperationResultInterface
    {
        $bxQuery = BxQueryAdapter::init($query);
        $pkListForDelete = $this->getPkValuesByQuery($bxQuery);
        if (empty($pkListForDelete)) {
            return new OperationResult('Данные для удаления не найдены', ['query' => $query]);
        }

        $mainResult = null;
        $dataResult = ['query' => $query];
        foreach ($pkListForDelete as $pkValue) {
            $isSuccess = (bool)CUser::Delete($pkValue);
            $updateResult = $isSuccess ?
                new OperationResult('', $dataResult, $pkValue) :
                new OperationResult('Ошибка удаления пользователя', $dataResult, $pkValue);

            if ($mainResult instanceof OperationResultInterface) {
                $mainResult->addNext($updateResult);
            } else {
                $mainResult = $updateResult;
            }
        }

        return $mainResult instanceof OperationResultInterface ?
            $mainResult :
            new OperationResult('Данные для удаления не найдены', $dataResult);
    }
}
