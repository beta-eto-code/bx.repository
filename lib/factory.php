<?php

namespace Bx\Repository;

use BX\Data\Provider\UserDataProvider;
use Bx\Repository\Models\UserShort;
use Exception;
use Repository\Base\BaseReadableRepository;
use Bitrix\Main\FileTable;
use Repository\Base\BaseRepository;
use Model\Base\Interfaces\ModelInterface;

class Factory
{
    public static function createFileRepository(): BaseReadableRepository
    {
        return new FileRepository();
    }

    /**
     * @throws Exception
     */
    public function createUserRepository(?string $userModelClass = null): BaseRepository
    {
        $userModelClass = $userModelClass ?? UserShort::class;
        if (!is_a($userModelClass, ModelInterface::class, true)) {
            throw new Exception('Invalid user model class');
        }
        return new BaseBxRepository(new UserDataProvider(), $userModelClass);
    }
}
