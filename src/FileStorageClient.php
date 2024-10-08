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
                return new S3FileStorage($config);
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
