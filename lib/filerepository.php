<?php

namespace Bx\Repository;
use Bitrix\Main\FileTable;
use BX\Data\Provider\DataManagerDataProvider;
use Bx\Repository\Models\File;
use CFile;
use Collection\Base\Collection;
use Collection\Base\Interfaces\CollectionInterface;
use Data\Provider\Interfaces\CompareRuleInterface;
use Data\Provider\QueryCriteria;
use Psr\Http\Message\UploadedFileInterface;
use Repository\Base\BaseReadableRepository;

class FileRepository extends BaseReadableRepository
{
    public function __construct()
    {
        $dataProvider = new DataManagerDataProvider(FileTable::class);
        parent::__construct($dataProvider);
    }
    
    protected function getClassModel(): string
    {
        return File::class;
    }

    protected function getFetcherList(): array
    {
        return [];
    }

    public function getResizedImageCollection(
        CollectionInterface $collection,
        int $width,
        int $height,
        ?int $mode = null
    ): CollectionInterface {
        $imageCollection = new Collection();
        foreach ($collection as $image) {
            if ($image instanceof File) {
                $imageCollection->append($this->getResizedImage($image, $width, $height, $mode));
            }
        }
        return $imageCollection;
    }

    public function getResizedImage(File $image, int $width, int $height, ?int $mode = null): File
    {
        $result = CFile::ResizeImageGet(
            $image->id,
            [
                'width' => $width,
                'height' => $height,
            ],
            $mode ?? BX_RESIZE_IMAGE_PROPORTIONAL,
            true
        );

        $image = clone $image;
        if (!empty($result)) {
            $image->width = (int) ($result['width'] ?? 0);
            $image->height = (int) ($result['height'] ?? 0);
            $image->src = (string) ($result['src'] ?? '');
        }
        return $image;
    }

    public function saveFile(
        string $baseDir,
        string $filePath,
        ?string $name = null,
        ?string $description = null
    ): ?File {
        $fileData = CFile::MakeFileArray($filePath);
        if (!empty($name)) {
            $fileData['name'] = $name;
        }

        if (!empty($description)) {
            $fileData['description'] = $description;
        }

        $newFile = $this->internalSaveFiles($baseDir, $fileData)->first();
        return $newFile instanceof File ? $newFile : null;
    }

    public function saveFiles(string $baseDir, string ...$filePaths): CollectionInterface
    {
        $fileDataList = [];
        foreach ($filePaths as $path) {
            $data = CFile::MakeFileArray($path);
            if (empty($data)) {
                continue;
            }

            $fileDataList[] = $data;
        }

        if (empty($fileDataList)) {
            return new Collection([]);
        }

        return $this->internalSaveFiles($baseDir, ...$fileDataList);
    }

    public function saveUploadFiles(string $baseDir, UploadedFileInterface ...$files): CollectionInterface
    {
        $fileDataList = [];
        foreach ($files as $file) {
            $data = $this->makeDataForSaveFile($file);

            $fileDataList[] = $data;
        }

        if (empty($fileDataList)) {
            return new Collection([]);
        }

        return $this->internalSaveFiles($baseDir, ...$fileDataList);
    }

    public function makeDataForSaveFile(UploadedFileInterface $file): array
    {
        return [
            'name' => $file->getClientFilename(),
            'size' => $file->getSize(),
            'tmp_name' => $file->getStream()->getMetadata('uri'),
            'type' => $file->getClientMediaType(),
            'MODULE_ID' => 'bx.model',
        ];
    }

    private function internalSaveFiles(string $baseDir, array ...$fileDataList): CollectionInterface
    {
        $fileIdList = [];
        foreach ($fileDataList as $data) {
            $fileIdList[] = (int) CFile::SaveFile($data, $baseDir);
        }

        if (empty($fileIdList)) {
            return new Collection([]);
        }

        $query = new QueryCriteria();
        $query->addCriteria('ID', CompareRuleInterface::IN, $fileIdList);
        return $this->getCollection($query);
    }
}
