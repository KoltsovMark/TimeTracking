<?php

declare(strict_types=1);

namespace App\Service\File;

use App\Contract\File\FileWriterInterface;

/**
 * Class WriterService
 * @package App\Service\File
 */
abstract class AbstractWriterService implements FileWriterInterface
{
    /**
     * @var string
     */
    protected string $extension = 'txt';

    /**
     * @param string $fileName
     * @param $data
     * @param string|null $path
     *
     * @return string
     */
    abstract public function write(string $fileName, $data, string $path = null): string;

    /**
     * @param $data
     */
    abstract public function validate($data): void;

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $fileName
     * @param string|null $path
     *
     * @return string
     */
    public function getFullPath(string $fileName, string $path = null): string
    {
        if (empty($path)) {
            $fullPath = $fileName;
        } else {
            $fullPath = "{$path}/{$fileName}";
        }

        return "{$fullPath}.{$this->getExtension()}";
    }
}