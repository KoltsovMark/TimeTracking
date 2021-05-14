<?php

declare(strict_types=1);

namespace App\Service\File;

use App\Contract\File\FileWriterInterface;

/**
 * Class WriterService.
 */
abstract class AbstractWriterService implements FileWriterInterface
{
    protected string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    protected string $extension = 'txt';

    /**
     * @param $data
     */
    abstract public function write(string $fileName, $data, string $path = null): string;

    /**
     * @param $data
     */
    abstract public function validate($data): void;

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getFullPath(string $fileName, string $path = null): string
    {
        if (empty($path)) {
            $fullPath = $fileName;
        } else {
            $fullPath = implode(DIRECTORY_SEPARATOR, [
                $this->projectDir,
                'public',
                $path,
                $fileName,
            ]);
        }

        return "{$fullPath}.{$this->getExtension()}";
    }

    public function createDirectoryIfDoNotExist(string $path): void
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
}
