<?php

namespace Bx\Repository\Models;

use Bx\Repository\Factory;
use Bx\Repository\Interfaces\FetchableInterface;
use Exception;
use Model\Base\Interfaces\ModelInterface;
use Repository\Base\Fetchers\RepositoryFetcherBuilder;

class UserShortWithPhoto extends UserShort implements FetchableInterface
{
    public ?File $photo = null;
    public string $photoId = '';

    public static function initFromArray(array $data): ModelInterface
    {
        $model = parent::initFromArray($data);
        $model->photoId = $data['PERSONAL_PHOTO'] ?? null;
        return $model;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lastName' => $this->lastName,
            'secondName' => $this->secondName,
            'email' => $this->email,
            'photo' => $this->photo instanceof File ? $this->photo->src : '',
            'attributes' => $this->attributes,
        ];
    }

    public static function getSelectFiledNames(): array
    {
        $select = parent::getSelectFiledNames();
        $select[] = 'PERSONAL_PHOTO';
        return $select;
    }

    /**
     * @throws Exception
     */
    public static function getFetchList(): array
    {
        return [
            'photo' => RepositoryFetcherBuilder::init(Factory::createFileRepository())
                ->setForeignKeyName('photoId')
                ->setDestinationKeyName('ID')
                ->setFillingKeyName('photo')
                ->setCompareCallback(function (UserShortWithPhoto $user, File $file): bool {
                    return $user->photoId === $file->id;
                })
                ->build(),
        ];
    }
}
