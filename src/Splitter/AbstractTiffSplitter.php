<?php
namespace SplitFile\Splitter;

abstract class AbstractTiffSplitter extends AbstractSplitter
{
    /**
     * Get the TIFF page count.
     *
     * @throws RuntimeException When cannot get count
     * @param string $filePath
     * @return int
     */
    public function getPageCount($filePath)
    {
        $commandArgs = [
            $this->getCommandPath('identify'),
            escapeshellarg($filePath),
        ];
        $output = $this->execute(implode(' ', $commandArgs));
        $pages = count(explode("\n", $output));
        if (0 === $pages) {
            $message = sprintf('Cannot get TIFF page count: %s', $filePath);
            throw new \RuntimeException($message);
        }
        return (int) $pages;
    }
}
