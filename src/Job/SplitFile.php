<?php
namespace SplitFile\Job;

use Doctrine\ORM\EntityManager;
use Omeka\Api\Manager;
use Omeka\Entity\Item;
use Omeka\Entity\Media;
use Omeka\Job\AbstractJob;
use Omeka\Api\Adapter\Manager as AdapterManager;

class SplitFile extends AbstractJob
{
    public function perform()
    {
        $services = $this->getServiceLocator();
        /** @var Manager $api */
        $api = $services->get('Omeka\ApiManager');
        /** @var EntityManager $em */
        $em = $services->get('Omeka\EntityManager');

        $store = $services->get('Omeka\File\Store');
        $config = $services->get('Config');

        /** @var Media $media */
        $media = $em->find('Omeka\Entity\Media', $this->getArg('media_id'));
        /** @var Item $item */
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

        // Make sure database connection is alive after processing data
        /** @var \Doctrine\DBAL\Connection $conn */
        $conn = $services->get('Omeka\Connection');
        if ( !$conn->isConnected() || !$conn->executeQuery('Select 1;') ) {
            $conn->connect();
        }

        // Build the media data, starting with existing media.
        $itemData = $item->getValues()->toArray();

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

        $itemData['o:media'] = $mediaData;

        // Update the item
        // problem: ImageServer Tile Builder does not run on partial updates
        // fix: do partial update, disable post events, modify original request and trigger events manually

        $response = $api->update(
            'items',
            $item->getId(),
            ['o:media' => $mediaData],
            [],
            [ 'isPartial' => true, 'finalize' => false]
        );

        /** @var AdapterManager $adapterManager */
        $adapterManager = $services->get('Omeka\ApiAdapterManager');
        $api->finalize(
            $adapterManager->get('items'),
            $response->getRequest()->setOption('isPartial', false),
            $response
        );
    }
}
