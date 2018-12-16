<?php
namespace SplitFile\Splitter\Pdf;

use SplitFile\Splitter\AbstractSplitter;

/**
 * Use pdfseparate to split PDF files into component PDF pages.
 *
 * @see https://www.mankier.com/1/pdfseparate
 */
class Pdf extends AbstractSplitter
{
    public function isAvailable()
    {
        return (bool) $this->cli->getCommandPath('pdfseparate');
    }

    public function split($filePath, $targetDir)
    {
        $commandPath = $this->cli->getCommandPath('pdfseparate');
        $uniqueId = uniqid();
        $pagePattern = sprintf('%s/%s-%%d.pdf', $targetDir, $uniqueId);
        $commandArgs = [
            $commandPath,
            escapeshellarg($filePath),
            escapeshellarg($pagePattern),
        ];
        $this->cli->execute(implode(' ', $commandArgs));
        $filePaths = glob(sprintf('%s/%s-*.pdf', $targetDir, $uniqueId));
        natsort($filePaths);
        return $filePaths;
    }
}
