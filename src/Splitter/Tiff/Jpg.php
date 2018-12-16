<?php
namespace SplitFile\Splitter\Tiff;

use SplitFile\Splitter\AbstractSplitter;

/**
 * Use convert to split TIFF files into component JPG pages.
 *
 * @see https://linux.die.net/man/1/convert
 */
class Jpg extends AbstractSplitter
{
    public function isAvailable()
    {
        return (bool) $this->cli->getCommandPath('convert');
    }

    public function split($filePath, $targetDir)
    {
        return [];
    }
}
