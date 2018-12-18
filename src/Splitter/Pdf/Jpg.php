<?php
namespace SplitFile\Splitter\Pdf;

use SplitFile\Splitter\AbstractPdfSplitter;

/**
 * Use convert to split PDF files into component JPG pages.
 *
 * @see https://linux.die.net/man/1/convert
 */
class Jpg extends AbstractPdfSplitter
{
    public function isAvailable()
    {
        return ((bool) $this->cli->getCommandPath('pdfinfo')
            && (bool) $this->cli->getCommandPath('convert'));
    }

    public function split($filePath, $targetDir, $pageCount)
    {
        return $this->splitUsingConvert(
            $filePath,
            $targetDir,
            $pageCount,
            ['from_pdf' => true]
        );
    }
}
