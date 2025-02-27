<?php

namespace Bx\Repository\Models;

use Access\Scope\Interfaces\AccessRecipientContextInterface;
use Bx\Repository\Interfaces\SavableInterface;
use Bx\Repository\Interfaces\SelectableInterface;
use Data\Provider\Interfaces\CompareRuleInterface;
use Data\Provider\Interfaces\DataProviderInterface;
use Data\Provider\Interfaces\PkOperationResultInterface;
use Data\Provider\QueryCriteria;
use Model\Base\BaseModel;
use Model\Base\Interfaces\ModelInterface;
use Model\Base\SerializeStrategy;

class UserShort extends BaseModel implements SelectableInterface, SavableInterface
{
    private static array $attributesForSelect = [];

    public string $id = '';
    public string $name = '';
    public string $lastName = '';
    public string $secondName = '';
    public string $email = '';
    /**
     * @var array<string, mixed>
     */
    public array $attributes = [];

    public static function withAttributes(string ...$attributes): void
    {
        self::$attributesForSelect = $attributes;
    }

    public static function initFromArray(array $data): ModelInterface
    {
        $model = new static();
        $model->id = $data['ID'];
        $model->name = $data['NAME'];
        $model->lastName = $data['LAST_NAME'];
        $model->secondName = $data['SECOND_NAME'];
        $model->email = $data['EMAIL'];
        $camelCaseSerializer = SerializeStrategy::getCamelCase();
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'UF_' )) {
                $attributeName = $camelCaseSerializer->getNewKey(substr($key, 3));
                $model['attributes'][$attributeName] = $value;
            }
        }

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
            'attributes' => $this->attributes,
        ];
    }

    public static function getSelectFiledNames(): array
    {
        $select = ['ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'EMAIL'];
        return array_merge($select, self::getAttributesForSelect());
    }

    private static function getAttributesForSelect(): array
    {
        if (empty(static::$attributesForSelect)) {
            return [];
        }

        $list = [];
        $snakeCaseSerializer = SerializeStrategy::getSnakeCase();
        foreach (static::$attributesForSelect as $attributeName) {
            $list[] = 'UF_' . strtoupper($snakeCaseSerializer->getNewKey($attributeName));
        }
        return $list;
    }

    public function save(
        DataProviderInterface $dataProvider,
        ?AccessRecipientContextInterface $recipientContext = null
    ): PkOperationResultInterface {
        $dataForSave = [
            'NAME' => $this->name,
            'LAST_NAME' => $this->lastName,
            'SECOND_NAME' => $this->secondName,
            'EMAIL' => $this->email
        ];

        $query = null;
        $isNewRecord = empty($this->id);
        if (!$isNewRecord) {
            $dataForSave['PASSWORD'] = md5(uniqid(rand(), true));
            $query = new QueryCriteria();
            $query->addCriteria('ID', CompareRuleInterface::EQUAL, $this->id);
        }

        $saveResult = $dataProvider->save($dataForSave, $query);
        if ($isNewRecord && !$saveResult->hasError()) {
            $this->id = $saveResult->getPk();
        }
        return $saveResult;
    }
}
