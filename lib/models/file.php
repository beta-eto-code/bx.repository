<?php

namespace Bx\Repository\Models;

use Bitrix\Main\Type\DateTime;
use Model\Base\BaseModel;
use Model\Base\Interfaces\ModelInterface;
use CFile;

class File extends BaseModel
{
    public string $id = '';
    public string $name = '';
    public string $type = '';
    public string $src = '';
    public ?DateTime $dateCreate = null;
    public int $size = 0;
    public int $width = 0;
    public int $height = 0;
    public string $description = '';

    public static function initFromArray(array $data): ModelInterface
    {
        $model = new File;
        $model->id = (string) ($data['ID'] ?? '');
        $model->name = (string) ($data['NAME'] ?? '');
        $model->type = (string) ($data['CONTENT_TYPE'] ?? '');
        $model->src = (string)CFile::GetFileSRC($data);
        $model->size = (int) ($data['FILE_SIZE'] ?? 0);
        $model->width = (int) ($data['WIDTH'] ?? 0);
        $model->height = (int) ($data['HEIGHT'] ?? 0);
        $model->description = (string) ($data['DESCRIPTION'] ?? '');
        $created = $data['TIMESTAMP_X'] ?? null;
        $model->dateCreate = $created instanceof DateTime ? $created : null;
        return $model;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'src' => $this->src,
            'dateCreate' => $this->dateCreate instanceof DateTime ? $this->dateCreate->format('c') : '',
            'size' => $this->size,
            'width' => $this->width,
            'height' => $this->height,
            'description' => $this->description,
        ];
    }
}
