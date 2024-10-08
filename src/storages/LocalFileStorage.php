<?php

namespace Dragoblued\Filestorageclient\storages;

use Gaufrette\Adapter\Local as LocalAdapter;
use Gaufrette\Filesystem;
use Dragoblued\Filestorageclient\exceptions\LocalFileStorageException;
use Dragoblued\Filestorageclient\interfaces\FileStorageInterface;
use Dragoblued\Filestorageclient\File;
use Throwable;

/**
 * Class LocalFileStorage
 */
class LocalFileStorage implements FileStorageInterface
{
    private Filesystem $filesystem;
    private $localAdapter;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->localAdapter = new LocalAdapter($config['path']);
        $this->filesystem = new Filesystem($this->localAdapter);
    }

    /**
     * @param string $name
     * @param string $tmpName
     *
     * @return void
     */
    public function upload(string $name, string $tmpName): void
    {
        try {
            $this->filesystem->write($name, file_get_contents($tmpName));
        } catch (Throwable $e) {
            throw new LocalFileStorageException('Unable to delete file: ' . $name . ' ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $name
     *
     * @return ?File
     */
    public function getFile(string $name): ?File
    {
        try {
            if ($this->filesystem->has($name)) {
                return (new File($name, $this->filesystem));
            }
            return null;
        } catch (Throwable $e) {
            throw new LocalFileStorageException('Error getting file: ' . $e->getMessage(), 0, $e);
        }
    }
}
