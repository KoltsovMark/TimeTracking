<?php

declare(strict_types=1);

namespace App\Dto\Api\File\Configuration;

class PdfWriterConfiguration
{
    public const PAPER_A4 = 'A4';
    public const ORIENTATION_PORTRAIT = 'portrait';

    private string $paper;
    private string $orientation;

    public function __construct()
    {
        $this->setPaper(self::PAPER_A4)
            ->setOrientation(self::ORIENTATION_PORTRAIT)
        ;
    }

    public function getPaper(): string
    {
        return $this->paper;
    }

    public function setPaper(string $paper): PdfWriterConfiguration
    {
        $this->paper = $paper;

        return $this;
    }

    public function getOrientation(): string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): PdfWriterConfiguration
    {
        $this->orientation = $orientation;

        return $this;
    }
}
