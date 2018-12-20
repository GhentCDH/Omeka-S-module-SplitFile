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

    public function filterMediaData(array $mediaData, $filePath, $pageCount,
        $splitFilePath, $page
    ) {
        return $mediaData;
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
     * Split a file using the convert command.
     *
     * Can't reliably split large files with one command due to ImageMagick
     * resource limits on some systems (the "convert: cache resources exhausted"
     * error). Instead, this executes the command in 2-page batches (any more
     * was unsuccessful on very large files during testing). Users may want to
     * increase the resource memory value in Imagemagick's policy.xml file if
     * this continues to cause problems. Run "identify -list resource" to see
     * current resource limits.
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
    public function splitUsingConvert($filePath, $targetDir, $pageCount,
        array $options = []
    ) {
        $uniqueId = uniqid();
        $pagePattern = sprintf('%s/%s-%%d.jpg', $targetDir, $uniqueId);
        $indexes = range(0, $pageCount - 1);
        foreach (array_chunk($indexes, 2) as $indexChunk) {
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
