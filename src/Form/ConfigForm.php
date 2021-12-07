<?php
namespace SplitFile\Form;

use Omeka\Form\Element\PropertySelect;
use Omeka\Permissions\Acl;
use Omeka\Settings\Settings;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Laminas\I18n\Translator\TranslatorAwareInterface;

class ConfigForm extends Form implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @var Settings
     */

    public function init()
    {
        $this->setAttribute('id', 'config-form');
        $this->add([
            'name' => 'splitfile_jpeg_density',
            'type' => Element\Select::class,
            'options' => [
                'label' => "PDF split jpeg density", // @translate
                'value_options' => [
                    '150' => '150 dpi',
                    '300' => '300 dpi',
                    '600' => '600 dpi',
                ]
            ],
            'attributes' => [
                'id' => 'splitfile_jpeg_density',
                'required' => true,
            ],
        ]);
    }


    protected function translate($args): string
    {
        $translator = $this->getTranslator();
        return $translator->translate($args);
    }

}