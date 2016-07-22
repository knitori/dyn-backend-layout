<?php
defined('TYPO3_MODE') or die();

$columns = [
    'lfm_backend_layout' => [
        'exclude' => 1,
        'label' => 'Select Layouts',
        'config' => [
            'type' => 'select',
            'renderType' => 'flexLayout',
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages',
    $columns
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'flex_layouts',
    'lfm_backend_layout',
    ''
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '--div--;Flexible Layout, --palette--;Flexible Layout;flex_layouts',
    '',
    ''
);
