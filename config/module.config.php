<?php
return [
    'service_manager' => [
        'factories' => [
            'SplitFile\SplitterManager' => SplitFile\Service\Splitter\ManagerFactory::class,
        ],
    ],
    'split_file_media_type_managers' => [
        'factories' => [
            'application/pdf' => SplitFile\Service\Splitter\Pdf\ManagerFactory::class,
            'image/tiff' => SplitFile\Service\Splitter\Tiff\ManagerFactory::class,
        ],
    ],
    'split_file_splitters_pdf' => [
        'factories' => [
            'jpg' => SplitFile\Service\Splitter\Pdf\JpgFactory::class,
            'pdf' => SplitFile\Service\Splitter\Pdf\PdfFactory::class,
        ],
    ],
    'split_file_splitters_tiff' => [
        'factories' => [
            'jpg' => SplitFile\Service\Splitter\Tiff\JpgFactory::class,
        ],
    ],
    'media_ingesters' => [
        'abstract_factories' => [
            SplitFile\Service\Media\Ingester\AbstractFactory::class,
        ],
    ],
];
