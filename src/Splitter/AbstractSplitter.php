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
     * Get a command path.
     *
     * @throws RuntimeException When cannot get command path
     * @param string $command
     * @return string
     */
    public function getCommandPath($command)
    {
        $output = $this->cli->getCommandPath($command);
        if (false === $output) {
            $message = sprintf('Cannot get command path: %s', $command);
            throw new \RuntimeException($message);
        }
        return $output;
    }

    /**
     * Execute a command.
     *
     * @throws RuntimeException When cannot execute command
     * @param string $command
     * @return string
     */
    public function execute($command)
    {
        $output = $this->cli->execute($command);
        if (false === $output) {
            $message = sprintf('Cannot execute command: %s', $command);
            throw new \RuntimeException($message);
        }
        return $output;
    }

    /**
     * Get the PDF page count.
     *
     * @throws RuntimeException When cannot get count
     * @param string $filePath
     * @return int
     */
    public function getPdfPageCount($filePath)
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
    /**
     * Get the TIFF page count.
     *
     * @throws RuntimeException When cannot get count
     * @param string $filePath
     * @return int
     */
    public function getTiffPageCount($filePath)
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
        $uniqueId = uniqid();
        $pagePattern = sprintf('%s/%s-%%d.jpg', $targetDir, $uniqueId);
        $indexes = range(0, $pageCount - 1);
        foreach (array_chunk($indexes, 10) as $indexChunk) {
            $range = sprintf('%s-%s', reset($indexChunk), end($indexChunk));
            $filePathWithRange = sprintf('%s[%s]', $filePath, $range);
            $args = [$this->getCommandPath('convert')];
            if (isset($options['from_pdf']) && $options['from_pdf']) {
                $args[] = '-density 150';
            }
            $args[] = escapeshellarg($filePathWithRange);
            $args[] = '-auto-orient';
            $args[] = '-background white';
            $args[] = '+repage';
            $args[] = '-alpha remove';
            $args[] = escapeshellarg($pagePattern);
            $this->execute(implode(' ', $args));
        }
        $filePaths = glob(sprintf('%s/%s-*.jpg', $targetDir, $uniqueId));
        natsort($filePaths);
        return $filePaths;
    }
}
