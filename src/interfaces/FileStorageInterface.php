<?php

namespace Dragoblued\Filestorageclient\interfaces;


/**
 * Интерфейс клиента хранилища файлов.
 */
interface FileStorageInterface
{
    /**
     * @param string $name
     * @param string $fileContent
     *
     * @return void
     */
    public function upload(string $name, string $fileContent): void;

    /**
     * @param string $name
     *
     * @return void
     */
    public function delete(string $name): void;

    /**
     * @param string $name
     *
     *
     */
    public function get(string $name);
}