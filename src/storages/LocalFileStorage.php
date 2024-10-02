<?php

namespace Dragoblued\Filestorageclient\storages;

use common\services\tabaeva\interfaces\FileStorageInterface;
use Exception;

/**
 * Class LocalFileStorage
 */
class LocalFileStorage implements FileStorageInterface
{
    private string $attachmentSystemPath;
    private string $attachmentPath;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->attachmentSystemPath = $config['attachmentSystemPath'];
        $this->attachmentPath = $config['attachmentPath'];
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
        $systemPath = $this->attachmentSystemPath.$path;
        if (!file_exists($systemPath)) {
            if (!mkdir($systemPath, 0777, true)) {
                throw new Exception('No write permission');
            }
        }
        if (!file_put_contents($systemPath . $name, $tmp)) {
            throw new Exception('Unable to write file');
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
        $systemPath = $this->attachmentSystemPath.$path.$name;
        if (file_exists($systemPath)) {
            unlink($systemPath);
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

    /**
     * @param string $attachmentPath
     *
     * @return void
     */
    public function setAttachmentPath(string $attachmentPath): void
    {
        $this->attachmentPath = $attachmentPath;
    }
}