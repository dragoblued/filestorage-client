<?php
namespace Dragoblued\Filestorageclient;

use Gaufrette\File as GaufretteFile;
use Gaufrette\Filesystem;
use Dragoblued\Filestorageclient\exceptions\FileException;
use Throwable;

/**
 * Class File
 */
class File
{
    private $file;

    /**
     * @param string $key
     * @param array  $config
     */

    public function __construct(string $key, Filesystem $fileSystem)
    {
        $this->file = new GaufretteFile($key, $fileSystem);
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->file->getSize();
    }

    /**
     * @return string
     */
    public function getContent($metadata = [])
    {
        return $this->file->getContent($metadata);
    }

    /**
     * @return bool
     */
    public function delete($metadata = []) :bool
    {
        try {
            return $this->file->delete($metadata);
        } catch (Throwable $e) {
            throw new FileException("Error to delete file: " . $e->getMessage(), 0, $e);
        }
    }
}
