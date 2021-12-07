<?php
namespace SplitFile\Splitter\Tiff;

use SplitFile\Splitter\AbstractTiffSplitter;

/**
 * Use convert to split TIFF files into component JPG pages.
 *
 * @see https://linux.die.net/man/1/convert
 */
class Jpg extends AbstractTiffSplitter
{

    public function isAvailable()
    {
        return ((bool) $this->cli->getCommandPath('identify')
            && (bool) $this->cli->getCommandPath('convert'));
    }

    public function split($filePath, $targetDir, $pageCount)
    {
        return $this->splitUsingConvert($filePath, $targetDir, $pageCount);
    }
}
