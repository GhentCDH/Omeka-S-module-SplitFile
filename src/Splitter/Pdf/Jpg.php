<?php
namespace SplitFile\Splitter\Pdf;

use SplitFile\Splitter\AbstractSplitter;

/**
 * Use convert to split PDF files into component JPG pages.
 *
 * @see https://linux.die.net/man/1/convert
 */
class Jpg extends AbstractSplitter
{
    public function isAvailable()
    {
        return ((bool) $this->cli->getCommandPath('convert')
            && (bool) $this->cli->getCommandPath('pdfinfo'));
    }

    public function split($filePath, $targetDir)
    {
        $pageCount = $this->getPdfPageCount($filePath);
        return $this->splitUsingConvert($filePath, $targetDir, $pageCount, ['from_pdf' => true]);
    }
}
