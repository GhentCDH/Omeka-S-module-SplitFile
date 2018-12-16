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
     * Split a file into its component pages.
     *
     * If the split was successful, this returns an array containing the split
     * file paths, in original order. If the split fails for any reason, this
     * should return false.
     *
     * @param string $filePath The path to the file
     * @param srtring $targetDir The path of the dir to process files
     * @return array|false
     */
    public function split($filePath, $targetDir);
}
