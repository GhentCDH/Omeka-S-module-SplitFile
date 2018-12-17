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

    /**
     * Split a file using the convert command.
     *
     * Can't reliably split large files with one command due to ImageMagick
     * resource limits on some systems ("cache resources exhausted" errors).
     * Instead, execute the command in 10-page batches.
     *
     * Options:
     *   - from_pdf: Set to true if the file to split is a PDF file
     *
     * @param string $filePath The path to the file
     * @param srtring $targetDir The path of the dir to process files
     * @param int $pageCount The page count of the file
     * @param array $options
     * @return array|false
     */
    public function splitUsingConvert($filePath, $targetDir, $pageCount, array $options = [])
    {
        $commandPath = $this->cli->getCommandPath('convert');
        $uniqueId = uniqid();
        $pagePattern = sprintf('%s/%s-%%d.jpg', $targetDir, $uniqueId);
        $indexes = range(0, $pageCount - 1);
        foreach (array_chunk($indexes, 10) as $indexChunk) {
            $range = sprintf('%s-%s', reset($indexChunk), end($indexChunk));
            $filePathWithRange = sprintf('%s[%s]', $filePath, $range);
            $args = [$commandPath];
            if (isset($options['from_pdf']) && $options['from_pdf']) {
                $args[] = '-density 150';
            }
            $args[] = escapeshellarg($filePathWithRange);
            $args[] = '-auto-orient';
            $args[] = '-background white';
            $args[] = '+repage';
            $args[] = '-alpha remove';
            $args[] = escapeshellarg($pagePattern);
            $command = implode(' ', $args);
            $this->cli->execute($command);
        }
        $filePaths = glob(sprintf('%s/%s-*.jpg', $targetDir, $uniqueId));
        natsort($filePaths);
        return $filePaths;
    }
}
