<?php
namespace SplitFile\Service\Splitter;

use SplitFile\Splitter\Manager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $config = $services->get('Config');
        return new Manager($services, $config['split_file_media_type_managers']);
    }
}
