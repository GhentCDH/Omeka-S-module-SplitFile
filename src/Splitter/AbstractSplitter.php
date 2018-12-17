<?php
namespace SplitFile\Splitter;

use Omeka\Stdlib\Cli;

abstract class AbstractSplitter implements SplitterInterface
{
    protected $cli;

    public function __construct(Cli $cli)
    {
        $this->cli = $cli;
    }

    /**
     * Get the PDF page count.
     *
     * @param string $filePath
     * @return int
     */
    public function getPdfPageCount($filePath)
    {
        $commandPath = $this->cli->getCommandPath('pdfinfo');
        $commandArgs = [
            $commandPath,
            escapeshellarg($filePath),
        ];
        $output = $this->cli->execute(implode(' ', $commandArgs));
        preg_match('/\nPages:\s+(\d+)\n/', $output, $matches);
        return (int) $matches[1];
    }
    /**
     * Get the TIFF page count.
     *
     * @param string $filePath
     * @return int
     */
    public function getTiffPageCount($filePath)
    {
        $commandPath = $this->cli->getCommandPath('identify');
        $commandArgs = [
            $commandPath,
            escapeshellarg($filePath),
        ];
        $output = $this->cli->execute(implode(' ', $commandArgs));
        $pages = count(explode("\n", $output));
        return (int) $pages;
    }
}
