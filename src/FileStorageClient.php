<?php
namespace Dragoblued\Filestorageclient;

use Dragoblued\Filestorageclient\enums\StorageTypeEnum;
use Dragoblued\Filestorageclient\exceptions\StorageException;
use Dragoblued\Filestorageclient\storages\S3FileStorage;
use Dragoblued\Filestorageclient\storages\LocalFileStorage;

/**
 * Class FileStorageClient
 */
class FileStorageClient
{
    public $fileStorage;

    /**
     * @param string $type
     * @param array  $config
     */
    public function __construct($type, $config = [])
    {
        $this->fileStorage = $this->getStorage($type, $config);
    }

    /**
     * @param string $type
     * @param array  $config
     */
    public function getStorage($type, $config)
    {
        switch ($type) {
            case StorageTypeEnum::S3:
                return new S3FileStorage($config ?: [
                    'S3_REGION' => getenv('S3_REGION'),
                    'S3_KEY' => getenv('S3_KEY'),
                    'S3_SECRET' => getenv('S3_SECRET'),
                    'S3_ENDPOINT' => getenv('S3_ENDPOINT'),
                    'S3_BUCKET' => getenv('S3_BUCKET'),
                    'S3_ROOT_DIRECTORY' => getenv('S3_ROOT_DIRECTORY'),
                ]);
            case StorageTypeEnum::LOCAL:
                return new LocalFileStorage($config);
            default:
                throw new StorageException('Storage is not set correctly');
        }
    }

    public function __call($methodName, $arguments)
    {
        if (method_exists($this->fileStorage, $methodName)) {
            return call_user_func_array([$this->fileStorage, $methodName], $arguments);
        } else {
            throw new StorageException('Method' . $methodName . 'not found');
        }
    }
}