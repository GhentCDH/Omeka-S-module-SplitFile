<?php
namespace SplitFile\Splitter;

abstract class AbstractPdfSplitter extends AbstractSplitter
{
    /**
     * Get the PDF page count.
     *
     * @throws RuntimeException When cannot get count
     * @param string $filePath
     * @return int
     */
    public function getPageCount($filePath)
    {
        $commandArgs = [
            $this->getCommandPath('pdfinfo'),
            escapeshellarg($filePath),
        ];
        $output = $this->execute(implode(' ', $commandArgs));
        preg_match('/\nPages:\s+(\d+)\n/', $output, $matches);
        if (!isset($matches[1]) || !is_numeric($matches[1])) {
            $message = sprintf('Cannot get PDF page count: %s', $filePath);
            throw new \RuntimeException($message);
        }
        return (int) $matches[1];
    }
}
