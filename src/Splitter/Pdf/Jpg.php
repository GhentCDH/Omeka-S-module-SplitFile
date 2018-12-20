<?php
namespace SplitFile\Splitter\Pdf;

use ExtractText\Module;
use ExtractText\Extractor\Pdftotext;
use SplitFile\Splitter\AbstractPdfSplitter;

/**
 * Use convert to split PDF files into component JPG pages.
 *
 * @see https://linux.die.net/man/1/convert
 */
class Jpg extends AbstractPdfSplitter
{
    protected $extractTextModule;

    public function isAvailable()
    {
        return ((bool) $this->cli->getCommandPath('pdfinfo')
            && (bool) $this->cli->getCommandPath('convert'));
    }

    public function split($filePath, $targetDir, $pageCount)
    {
        return $this->splitUsingConvert(
            $filePath,
            $targetDir,
            $pageCount,
            ['from_pdf' => true]
        );
    }
    public function filterMediaData(array $mediaData, $filePath, $pageCount,
        $splitFilePath, $page
    ) {
        if (!$this->extractTextModule) {
            // The ExtractText module is not installed or active.
            return parent::filterMediaData($mediaData, $filePath, $pageCount, $splitFilePath, $page);
        }
        $textProperty = $this->extractTextModule->getTextProperty();
        if (false === $textProperty) {
            // The text property does not exist.
            return parent::filterMediaData($mediaData, $filePath, $pageCount, $splitFilePath, $page);
        }
        $extractor = new Pdftotext($this->cli);
        $text = $extractor->extract($filePath, ['f' => $page, 'l' => $page]);
        if (false === $text) {
            // Could not extract text from the page.
            return parent::filterMediaData($mediaData, $filePath, $pageCount, $splitFilePath, $page);
        }
        $mediaData['extracttext:extracted_text'] = [
            [
                'type' => 'literal',
                '@value' => $text,
                'property_id' => $textProperty->getId(),
            ]
        ];
        return $mediaData;
    }

    public function setExtractTextModule(Module $extractTextModule = null)
    {
        $this->extractTextModule = $extractTextModule;
    }
}
