<?php
defined('TYPO3_MODE') or die();

$columns = [
    'lfm_row_selection' => [
        'exclude' => 1,
        'label' => 'Select Layouts',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectBackendLayout',
//            'renderType' => 'selectMultipleSideBySide',
            'layoutsPath' => 'EXT:lfmtheme/Configuration/TSConfig/Puzzle',
            'itemsProcFunc' => 'LFM\Lfmtheme\UserFunc\ItemsProcFunc->getLayoutRows',
            'foreign_table' => 'tx_lfmtheme_domain_model_backendlayout',
            'multiple' => true,
            'minitems' => 0,
            'maxitems' => 999,
        ],
        'displayCond' => 'FIELD:backend_layout:=:select__selectTemplate',
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages',
    $columns
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages', 'layout', '--linebreak--, lfm_row_selection', 'after:backend_layout_next_level'
);
