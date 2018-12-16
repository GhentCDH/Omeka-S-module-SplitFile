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
        $commandArgs = [$commandPath, $filePath];
        $info = $this->cli->execute(implode(' ', $commandArgs));
        preg_match('/\nPages:\s+(\d+)\n/', $info, $matches);
        return (int) $matches[1];
    }
}
