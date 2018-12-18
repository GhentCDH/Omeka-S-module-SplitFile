<?php
namespace SplitFile\Splitter\Pdf;

use SplitFile\Splitter\AbstractPdfSplitter;

/**
 * Use pdfseparate to split PDF files into component PDF pages.
 *
 * @see https://www.mankier.com/1/pdfseparate
 */
class Pdf extends AbstractPdfSplitter
{
    public function isAvailable()
    {
        return ((bool) $this->cli->getCommandPath('pdfinfo')
            && (bool) $this->cli->getCommandPath('pdfseparate'));
    }

    public function split($filePath, $targetDir, $pageCount)
    {
        $uniqueId = uniqid();
        $pagePattern = sprintf('%s/%s-%%d.pdf', $targetDir, $uniqueId);
        $commandArgs = [
            $this->getCommandPath('pdfseparate'),
            escapeshellarg($filePath),
            escapeshellarg($pagePattern),
        ];
        $this->execute(implode(' ', $commandArgs));
        $filePaths = glob(sprintf('%s/%s-*.pdf', $targetDir, $uniqueId));
        natsort($filePaths);
        return $filePaths;
    }
}
