<?php

declare(strict_types=1);

namespace App\Contract\Task\File;

use App\Dto\Api\File\Configuration\PdfWriterConfiguration;

interface ConfigurablePdfWriterInterface
{
    public function getConfiguration(): PdfWriterConfiguration;

    public function setConfiguration(PdfWriterConfiguration $pdfWriterConfiguration): void;
}
