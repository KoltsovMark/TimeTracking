<?php

declare(strict_types=1);

namespace App\Contract\File;

interface FileWriterInterface
{
    /**
     * Return supported extension
     *
     * @return string
     */
    public function getExtension(): string;

    /**
     * Write data to file
     *
     * @param string $fileName
     * @param $data
     * @param string|null $path
     */
    public function write(string $fileName, $data, string $path = null): void;

    /**
     * Validate input data before storing
     *
     * @param $data
     */
    public function validate($data): void;
}