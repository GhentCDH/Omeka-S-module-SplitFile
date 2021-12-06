<?php
namespace SplitFile\Service\Splitter\Pdf;

use SplitFile\Splitter\Pdf\Jpg;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class JpgFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $splitter = new Jpg($services->get('Omeka\Cli'));
        $splitter->setExtractTextModule($services->get('ModuleManager')->getModule('ExtractText'));
        return $splitter;
    }
}
