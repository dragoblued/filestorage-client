<?php

namespace Dragoblued\Filestorageclient\Interfaces;


/**
 * Interface FileStorageInterface
 */
interface FileStorageInterface
{
    /**
     * @param string $name
     * @param string $tmp
     * @param string $path
     *
     * @return void
     */
    public function upload(string $name, string $tmp, string $path = ''): void;

    /**
     * @param string $name
     *
     * @return void
     */
    public function delete(string $name): void;
}