<?php

declare(strict_types=1);

namespace App\Contract\File;

interface FileWriterInterface
{
    /**
     * Return supported extension.
     */
    public function getExtension(): string;

    /**
     * Write data to file.
     *
     * @param $data
     */
    public function write(string $fileName, $data, string $path = null): string;

    /**
     * Validate input data before storing.
     *
     * @param $data
     */
    public function validate($data): void;

    public function getFullPath(string $fileName, string $path = null): string;

    /**
     * Create a directory on file system due to path.
     */
    public function createDirectoryIfDoNotExist(string $path);
}
