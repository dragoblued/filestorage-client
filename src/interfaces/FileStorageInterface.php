<?php

namespace Dragoblued\Filestorageclient\interfaces;

use Dragoblued\Filestorageclient\File;

/**
 * Interface FileStorageInterface
 */
interface FileStorageInterface
{
    /**
     * @param string $name
     * @param string $tmpName
     *
     * @return void
     */
    public function upload(string $name, string $tmpName): void;

    /**
     * @param string $name
     *
     * @return ?File
     */
    public function getFile(string $name): ?File;
}
