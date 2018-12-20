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

    /**
     * Filter media data before updating parent item.
     *
     * @param array $mediaData
     * @param string $filePath
     * @param int $pageCount The page count of the original file
     * @param string $splitFilePath
     * @param int $page The page of the split file
     */
    public function filterMediaData(array $mediaData, $filePath, $pageCount,
        $splitFilePath, $page
    );
}
