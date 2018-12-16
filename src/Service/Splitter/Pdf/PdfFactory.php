<?php
namespace SplitFile\Service\Splitter\Pdf;

use SplitFile\Splitter\Pdf\Pdf;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class PdfFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new Pdf($services->get('Omeka\Cli'));
    }
}
