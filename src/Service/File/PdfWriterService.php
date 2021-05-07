<?php

declare(strict_types=1);

namespace App\Service\File;

use App\Contract\Task\File\ConfigurablePdfWriterInterface;
use App\Exception\File\UnsupportedDataType;
use App\Model\Configuration\File\PdfWriterConfiguration;
use Dompdf\Dompdf;
use Symfony\Component\Filesystem\Filesystem;

class PdfWriterService extends AbstractWriterService implements ConfigurablePdfWriterInterface
{
    /**
     * @var string
     */
    protected string $extension = 'pdf';

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;
    /**
     * @var PdfWriterConfiguration
     */
    private PdfWriterConfiguration $configuration;

    /**
     * PdfWriterService constructor.
     *
     * @param Filesystem $filesystem
     * @param PdfWriterConfiguration $configuration
     */
    public function __construct(Filesystem $filesystem, PdfWriterConfiguration $configuration)
    {
        $this->filesystem = $filesystem;

        $this->setConfiguration($configuration);
    }

    /**
     * @return PdfWriterConfiguration
     */
    public function getConfiguration(): PdfWriterConfiguration
    {
        return $this->configuration;
    }

    /**
     * @param PdfWriterConfiguration $configuration
     */
    public function setConfiguration(PdfWriterConfiguration $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $fileName
     * @param $data
     * @param string|null $path
     *
     * @throws UnsupportedDataType
     */
    public function write(string $fileName, $data, string $path = null): string
    {
        $this->validate($data);

        $fullPath = $this->getFullPath($fileName, $path);
        $dompdf = new Dompdf();
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
        if ( ! is_string($data)) {
            throw new UnsupportedDataType();
        }
    }
}