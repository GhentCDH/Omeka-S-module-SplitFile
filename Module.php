<?php
namespace SplitFile;

if (!class_exists(\Generic\AbstractModule::class)) {
    require file_exists(dirname(__DIR__) . '/Generic/AbstractModule.php')
        ? dirname(__DIR__) . '/Generic/AbstractModule.php'
        : __DIR__ . '/src/Generic/AbstractModule.php';
}

use Omeka\File\Store\Local;
use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Generic\AbstractModule;


class Module extends AbstractModule
{
    const NAMESPACE = __NAMESPACE__;

    protected $dependency = "FileSideload";

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        // Attach listeners only if we can split files.
        if (!$this->canSplitFiles()) {
            return;
        }
        /**
         * Add a "Split file" tab to the media edit page.
         */
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.edit.section_nav',
            function (Event $event) {
                $view = $event->getTarget();
                $media = $view->media;
                if (!$this->getSplitterManager($media->mediaType())) {
                    return;
                }
                $sectionNavs = $event->getParam('section_nav');
                $sectionNavs['split-pdf'] = $view->translate('Split file');
                $event->setParam('section_nav', $sectionNavs);
            }
        );
        /**
         * Add a "Split file" section to the media edit page.
         */
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.edit.form.after',
            function (Event $event) {
                $view = $event->getTarget();
                $media = $view->media;
                $splitters = $this->getSplitterManager($media->mediaType());
                if (!$splitters) {
                    return;
                }
                $splitterOptions = sprintf(
                    '<option value="">%s</option>',
                    $view->translate('[No action]')
                );
                foreach ($splitters->getRegisteredNames() as $splitterName) {
                    $splitter = $splitters->get($splitterName);
                    if ($splitter->isAvailable()) {
                        $splitterOptions .= sprintf(
                            '<option value="%s">%s</option>',
                            $view->escapeHtml($splitterName),
                            $splitterName
                        );
                    }
                }
                $html = sprintf('
                    <div id="split-pdf" class="section">
                        <div class="field">
                            <div class="field-meta">
                                <label for="split_pdf_action">%s</label>
                            </div>
                            <div class="inputs">
                                <select name="split_file_splitter">%s</select>
                            </div>
                        </div>
                    </div>',
                    $view->translate('Split file'),
                    $splitterOptions
                );
                echo $html;
            }
        );
        $sharedEventManager->attach(
            'Omeka\Api\Adapter\MediaAdapter',
            'api.update.post',
            function (Event $event) {
                $services = $this->getServiceLocator();
                $request = $event->getParam('request');
                $response = $event->getParam('response');
                $requestData = $request->getContent();
                if (!isset($requestData['split_file_splitter'])) {
                    return;
                }
                $media = $response->getContent();
                $splitter = $this->getSplitter(
                    $media->getMediaType(),
                    $requestData['split_file_splitter']
                );
                if (!$splitter) {
                    return;
                }
                $jobDispatcher = $services->get('Omeka\Job\Dispatcher');
                $job = $jobDispatcher->dispatch('SplitFile\Job\SplitFile', [
                    'media_id' => $media->getId(),
                    'splitter' => $requestData['split_file_splitter'],
                ]);
            }
        );
    }

    /**
     * Determine whether the system meets the minimum requirements to split files.
     *
     * @return bool
     */
    public function canSplitFiles()
    {
        $services = $this->getServiceLocator();
        $store = $services->get('Omeka\File\Store');
        if (!($store instanceof Local)) {
            // Must use local storage.
            return false;
        }
        $modules = $services->get('Omeka\ModuleManager');
        $fileSideload = $modules->getModule('FileSideload');
        if (!$fileSideload || ('active' !== $fileSideload->getState())) {
            // The FileSideload module must be installed and active.
            return false;
        }
        return true;
    }

    /**
     * Get the splitter manager for a media type.
     *
     * @param string $mediaType
     * @return AbstractPluginManager
     */
    public function getSplitterManager($mediaType)
    {
        $services = $this->getServiceLocator();
        try {
            return $services->get('SplitFile\SplitterManager')->get($mediaType);
        } catch (ServiceNotFoundException $e) {
            // There are no splitters for this media type.
            return false;
        }
    }

    /**
     * Get the splitter for a media type using a specific name.
     *
     * @param string $mediaType
     * @param string $splitterName
     * @return SplitterInterface
     */
    public function getSplitter($mediaType, $splitterName)
    {
        $splitterManager = $this->getSplitterManager($mediaType);
        if (!$splitterManager) {
            // There are no splitters for this media type.
            return false;
        }
        try {
            return $splitterManager->get($splitterName);
        } catch (ServiceNotFoundException $e) {
            // There are no splitters for this media type using this name.
            return false;
        }
    }
}
