<?php

namespace Bx\Repository;

use Bx\Repository\DataProviders\UserDataProvider;
use Bx\Repository\Models\UserShort;
use Bitrix\Main\Result;
use Bitrix\Main\Error;

class UserRepository extends BaseBxRepository
{
    public function __construct(?string $modelClass = null, ?UserDataProvider $dataProvider = null)
    {
        $modelClass = $modelClass ?? UserShort::class;
        $dataProvider = $dataProvider ?? new UserDataProvider();
        parent::__construct($dataProvider, $modelClass);
    }

    public function updatePassword(mixed $userId, string $password): Result
    {
        if (empty($userId)) {
            return new Result(new Error('Не указан идентификатор пользователя'));
        }

        $dataForSave = ['ID' => $userId, 'PASSWORD' => $password];
        $saveResult = $this->dataProvider->save($dataForSave);
        if ($saveResult->hasError()) {
            return new Result(new Error($saveResult->getErrorMessage()));
        }

        return new Result();
    }
}
