<?php
namespace SplitFile\Splitter\Tiff;

use Omeka\ServiceManager\AbstractPluginManager;
use SplitFile\Splitter\SplitterInterface;

class Manager extends AbstractPluginManager
{
    protected $autoAddInvokableClass = false;

    protected $instanceOf = SplitterInterface::class;

    public function get($name, $options = [], $usePeeringServiceManagers = true)
    {
        return parent::get($name, $options, $usePeeringServiceManagers);
    }
}
