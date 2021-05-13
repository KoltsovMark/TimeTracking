<?php

declare(strict_types=1);

namespace App\Service\File;

use App\Contract\Task\File\ConfigurablePdfWriterInterface;
use App\Dto\Api\File\Configuration\PdfWriterConfiguration;
use App\Exception\File\UnsupportedDataType;
use Dompdf\Dompdf;
use Symfony\Component\Filesystem\Filesystem;

class PdfWriterService extends AbstractWriterService implements ConfigurablePdfWriterInterface
{
    protected string $extension = 'pdf';

    protected Filesystem $filesystem;

    protected PdfWriterConfiguration $configuration;

    /**
     * PdfWriterService constructor.
     */
    public function __construct(string $projectDir, Filesystem $filesystem, PdfWriterConfiguration $configuration)
    {
        parent::__construct($projectDir);

        $this->filesystem = $filesystem;

        $this->setConfiguration($configuration);
    }

    public function getConfiguration(): PdfWriterConfiguration
    {
        return $this->configuration;
    }

    public function setConfiguration(PdfWriterConfiguration $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $data
     *
     * @throws UnsupportedDataType
     */
    public function write(string $fileName, $data, string $path = null): string
    {
        $this->validate($data);

        $fullPath = $this->getFullPath($fileName, $path);
        $dompdf = $this->getDompdf();
        $dompdf->loadHtml($data);
        $dompdf->setPaper($this->getConfiguration()->getPaper(), $this->getConfiguration()->getOrientation());
        $dompdf->render();
        $this->filesystem->dumpFile($fullPath, $dompdf->output());

        return $fullPath;
    }

    /**
     * @param $data
     *
     * @throws UnsupportedDataType
     */
    public function validate($data): void
    {
        if (!is_string($data)) {
            throw new UnsupportedDataType();
        }
    }

    protected function getDompdf(): Dompdf
    {
        return new Dompdf();
    }
}
