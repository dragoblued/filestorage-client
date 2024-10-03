<?php

namespace Dragoblued\Filestorageclient\storages;

use Dragoblued\Filestorageclient\exceptions\LocalFileStorageException;
use Dragoblued\Filestorageclient\interfaces\FileStorageInterface;
use Throwable;

/**
 * Class LocalFileStorage
 */
class LocalFileStorage implements FileStorageInterface
{
    private string $attachmentSystemPath;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->attachmentSystemPath = $config['attachmentSystemPath'];
    }

    /**
     * @param string $name
     * @param string $tmp
     * @param string $path
     *
     * @return void
     */
    public function upload(string $name, string $tmp, string $path = ''): void
    {
        $systemPath = $this->attachmentSystemPath . $path;
        if (!file_exists($systemPath)) {
            if (!mkdir($systemPath, 0777, true)) {
                throw new LocalFileStorageException('No write permission');
            }
        }
        if (!file_put_contents($systemPath . $name, $tmp)) {
            throw new LocalFileStorageException('Unable to write file');
        }
    }

    /**
     * @param string $name
     * @param string $path
     *
     * @return void
     */
    public function delete(string $name, string $path = ''): void
    {
        $systemPath = $this->attachmentSystemPath . $path . $name;
        try {
            if (file_exists($systemPath)) {
                unlink($systemPath);
            }
        } catch (Throwable $e) {
            throw new LocalFileStorageException('Unable to delete file: ' . $e->getMessage());
        }
    }

    /**
     * @param string $attachmentSystemPath
     *
     * @return void
     */
    public function setAttachmentSystemPath(string $attachmentSystemPath): void
    {
        $this->attachmentSystemPath = $attachmentSystemPath;
    }
}