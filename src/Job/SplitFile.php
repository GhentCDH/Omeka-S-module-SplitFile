<?php
namespace SplitFile\Job;

use Omeka\Job\AbstractJob;
use Omeka\Job\Exception\RuntimeException;

class SplitFile extends AbstractJob
{
    public function perform()
    {
        $services = $this->getServiceLocator();
        $api = $services->get('Omeka\ApiManager');
        $em = $services->get('Omeka\EntityManager');
        $store = $services->get('Omeka\File\Store');
        $config = $services->get('Config');

        $media = $em->find('Omeka\Entity\Media', $this->getArg('media_id'));
        $item = $media->getItem();

        // Split the file.
        $splitter = $services->get('SplitFile\SplitterManager')
            ->get($media->getMediaType())
            ->get($this->getArg('splitter'));
        $filePath = $store->getLocalPath(sprintf('original/%s', $media->getFilename()));
        $pageCount = $splitter->getPageCount($filePath);
        $splitFilePaths = $splitter->split($filePath, $config['temp_dir'], $pageCount);
        if (!is_array($splitFilePaths)) {
            $message = sprintf(
                'Unexpected split() return value. Expected array got %s',
                gettype($splitFilePaths)
            );
            throw new \RuntimeException($message);
        }
        if ($pageCount !== count($splitFilePaths)) {
            $message = sprintf(
                'The file page count (%s) does not match the count returned by split() (%s).',
                $pageCount,
                count($splitFilePaths)
            );
            throw new \RuntimeException($message);
        }
        $splitFilePaths = array_values($splitFilePaths); // ensure sequential indexes

        // Build the media data, starting with existing media.
        $mediaData = [];
        foreach ($item->getMedia()->getKeys() as $itemMediaId) {
            $mediaData[] = ['o:id' => $itemMediaId];
        }
        $page = 1;
        foreach ($splitFilePaths as $splitFilePath) {
            $thisMediaData = [
                'o:source' => sprintf('%s-%s', $media->getSource(), $page),
                'o:is_public' => $media->isPublic(),
                'o:ingester' => 'splitfilesideload',
                'ingest_filename' => basename($splitFilePath),
            ];
            $mediaData[] = $splitter->filterMediaData(
                $thisMediaData, $filePath, $pageCount, $splitFilePath, $page
            );
            $page++;
        }

        // Update the item.
        $api->update('items', $item->getId(), ['o:media' => $mediaData]);
    }
}
