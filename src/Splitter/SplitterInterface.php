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
     * Used by splitter instances to filter the media data of an individual file
     * page. For example, the splitter could add page-specific metadata to the
     * newly created media.
     *
     * @param array $mediaData
     * @param string $filePath The path to the original file
     * @param int $pageCount The original file page count
     * @param string $splitFilePath The path to the split file page
     * @param int $page The page of the split file
     * @return array The filtered media data
     */
    public function filterMediaData(array $mediaData, $filePath, $pageCount,
        $splitFilePath, $page
    );
}
