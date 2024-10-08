<?php
namespace Dragoblued\Filestorageclient;

use Gaufrette\File as GaufretteFile;
use Gaufrette\Filesystem;

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
        return $this->file->getContent();
    }

    /**
     * @return bool
     */
    public function delete($metadata = []) :bool
    {
        return $this->file->delete($metadata);
    }
}
