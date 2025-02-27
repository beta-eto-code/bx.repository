# Пример реализации простого модуля 1C:Битрикс d7 

```php
use Model\Base\BaseModel;
use Model\Base\Interfaces\ModelInterface;
use Bitrix\Main\Type\DateTime;
use Bx\Repository\Interfaces\SelectableInterface;
use Bx\Repository\Interfaces\FetchableInterface;
use Bx\Repository\Interfaces\SavableInterface;
use Data\Provider\Interfaces\DataProviderInterface;

class ShortNews extends BaseModel implements SelectableInterface, FetchableInterface, SavableInterface
{
    public string $id = '';
    public string $title = '';
    public string $previewText = '';
    public string $previewPicture = '';
    public ?DateTime $publicationDate = null;

    public static function initFromArray(array $data): ModelInterface
    {
        $this->id = (string) ($data['ID'] ?? '');
        $this->titme = (string) ($data['NAME'] ?? '');
        $this->previewText = (string) ($data['PREVIEW_TEXT'] ?? '');
        $this->previewPicture = (string) ($data['PREVIEW_PICTURE'] ?? '');
        
        $publicationDate = $data['ACTIVE_FROM'] ?? null;
        if (is_string($publicationDate)) {
            $publicationDate = new DateTime($publicationDate, 'd.m.Y H:i:s');
        }
        
        $this->publicationDate = $publicationDate instanceof DateTime ? $publicationDate : null;
    }
    
    public function save(
        DataProviderInterface $dataProvider,
        ?\Repository\Base\Interfaces\AccessRecipientContextInterface $recipientContext = null
    ): PkOperationResultInterface {
        
    }
    
    public static function getSelectFiledNames(): array
    {
        return ['ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'CREATED_BY'];
    }
    
    public static function getFetchList(): array
    {
        return [];
    }
    
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'previewText' => $this->previewText,
            'previewPicture' => $this->previewPicture,
            'publicationDate' => $this->publicationDate instanceof DateTime ? $this->publicationDate->format('c') : '';
        ];
    }
}
```


```php
$iblockRepository = new \Bx\Repository\IblockElementRepository('content', 'news', ShorNews:class);
$iblockRepository->initWithModelClass(NewsModel::class)->getCollection();
```