Filestorage Client
Клиент предназначен для работы с файловыми хранилищами, включая использование S3

Установка пакета

composer require fixprice/filestorage-client


Пример использования

Сохранение файла в s3

use Fixprice\FilestorageClient\FilestorageClient;
use Fixprice\FilestorageClient\Enums\StorageTypeEnum;

$fileStorageClient = new FileStorageClient(StorageTypeEnum::S3); //принимает вторым аргументом настройки для S3. По умолчанию эти настройки заполняются из переменных окружения (env)
$fileStorageClient->fileStorage->upload($file->name, $file->tempName);

Описание методов для работы с S3:
1. upload: загрузка файла в хранилище.
2. get: получение файла из хранилища.
3. delete: удаление файла из хранилища.
4. size: получение размера файла.
5. url: получение ссылки на файл в S3.
6. clearBucket: очистка всего содержимого бакета.

Сохранение файла в локальном хранилище

use Fixprice\FilestorageClient\FilestorageClient;
use Fixprice\FilestorageClient\Enums\StorageTypeEnum;

$fileStorageClient = new FileStorageClient(StorageTypeEnum::LOCAL, [
    'attachmentSystemPath' => \Yii::getAlias('@common/upload/')
]);
$fileStorageClient->fileStorage->upload($file->name, $file->tempName);

1. upload: загрузка файла в хранилище.
2. delete: удаление файла из хранилища.
3. setAttachmentSystemPath: изменения системного пути

