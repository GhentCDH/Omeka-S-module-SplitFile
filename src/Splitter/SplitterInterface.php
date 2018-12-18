<?php
namespace SplitFile\Splitter;

/**
 * Interface for PDF splitters.
 */
interface SplitterInterface
{
    /**
     * Is this splitter available?
     *
     * @return bool
     */
    public function isAvailable();

    /**
     * Get the page count.
     *
     * @param string $filePath
     * @return int
     */
    public function getPageCount($filePath);

    /**
     * Split a file into its component pages.
     *
     * Returns an array containing the split file paths, in original order.
     *
     * @param string $filePath The path to the file
     * @param string $targetDir The path of the dir to process files
     * @param int $pageCount The file page count
     * @return array
     */
    public function split($filePath, $targetDir, $pageCount);
}
