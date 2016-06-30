<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Lichtflut.Medien - Theme',
    'description' => 'Base Extension for theme creation.',
    'category' => 'Theme',
    'author' => 'Lars P. Søndergaard',
    'author_company' => 'Lichtflut.Medien GmbH & Co. KG',
    'author_email' => 'l.soendergaard@lichtflut-medien.de',
    'state' => 'beta',
    'clearCacheOnLoad' => '1',
    'version' => '1.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-0.0.0',
        ],
        'suggests' => [
            'news' => '4.2.1-0.0.0',
        ],
    ]
];
