<?php
namespace SplitFile\Service\Media\Ingester;

use Interop\Container\ContainerInterface;
use FileSideload\Media\Ingester\Sideload;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Construct the "splitfilesideload" media ingester.
 *
 * Constructs the "sideload" media ingester provided by the FileSideload module,
 * but passes arguments specifically used by SplitFile. The idea is that file
 * splitters use the configured temp_dir (usually /tmp/) to store temporary page
 * files so the ingester can process the files during ingest.
 */
class AbstractFactory implements AbstractFactoryInterface
{
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return ('splitfilesideload' === $requestedName);
    }

    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $config = $services->get('Config');
        $directory = $config['temp_dir'];
        return new Sideload(
            $directory,
            is_writable($directory),
            $services->get('Omeka\File\TempFileFactory')
        );
    }
}
