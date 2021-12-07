<?php
namespace SplitFile;

return [
    'service_manager' => [
        'factories' => [
            'SplitFile\SplitterManager' => Service\Splitter\ManagerFactory::class,
        ],
    ],
    'split_file_media_type_managers' => [
        'factories' => [
            'application/pdf' => Service\Splitter\Pdf\ManagerFactory::class,
            'image/tiff' => Service\Splitter\Tiff\ManagerFactory::class,
        ],
    ],
    'split_file_splitters_pdf' => [
        'factories' => [
            'jpg' => Service\Splitter\Pdf\JpgFactory::class,
            'pdf' => Service\Splitter\Pdf\PdfFactory::class,
        ],
    ],
    'split_file_splitters_tiff' => [
        'factories' => [
            'jpg' => Service\Splitter\Tiff\JpgFactory::class,
        ],
    ],
    'media_ingesters' => [
        'abstract_factories' => [
            Service\Media\Ingester\AbstractFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            Form\ConfigForm::class => Service\Form\ConfigFormFactory::class,
        ],
    ],
    'splitfile' => [
        'config' => [
            'splitfile_jpeg_density' => '300'
        ]
    ]
];
