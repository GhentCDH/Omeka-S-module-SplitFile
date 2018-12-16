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
        $commandPath = $this->cli->getCommandPath('convert');
        $uniqueId = uniqid();
        $pagePattern = sprintf('%s/%s-%%d.jpg', $targetDir, $uniqueId);
        // Can't reliably split large PDFs with one command due to ImageMagick
        // resource limits on some systems ("cache resources exhausted" errors).
        // Instead, execute the command in 10-page batches.
        $pageCount = $this->getPdfPageCount($filePath);
        $indexes = range(0, $pageCount - 1);
        foreach (array_chunk($indexes, 10) as $indexChunk) {
            $range = sprintf('%s-%s', reset($indexChunk), end($indexChunk));
            $filePathWithRange = sprintf('%s[%s]', $filePath, $range);
            $commandArgs = [
                $commandPath,
                '-density 150',
                escapeshellarg($filePathWithRange),
                '-auto-orient',
                '-background white',
                '+repage',
                '-alpha remove',
                escapeshellarg($pagePattern),
            ];
            $this->cli->execute(implode(' ', $commandArgs));
        }
        $filePaths = glob(sprintf('%s/%s-*.jpg', $targetDir, $uniqueId));
        natsort($filePaths);
        return $filePaths;
    }
}
