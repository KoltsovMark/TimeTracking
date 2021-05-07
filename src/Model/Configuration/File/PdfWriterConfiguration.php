<?php

declare(strict_types=1);

namespace App\Model\Configuration\File;

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

    /**
     * @return string
     */
    public function getPaper(): string
    {
        return $this->paper;
    }

    /**
     * @param string $paper
     *
     * @return PdfWriterConfiguration
     */
    public function setPaper(string $paper): PdfWriterConfiguration
    {
        $this->paper = $paper;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrientation(): string
    {
        return $this->orientation;
    }

    /**
     * @param string $orientation
     *
     * @return PdfWriterConfiguration
     */
    public function setOrientation(string $orientation): PdfWriterConfiguration
    {
        $this->orientation = $orientation;
        return $this;
    }
}