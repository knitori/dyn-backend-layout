<?php

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutDataProvider']['select']
    = \LFM\Lfmtheme\Provider\SelectBackendLayoutDataProvider::class;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
    = \LFM\Lfmtheme\Hooks\PageSaveHook::class;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1467917110] = [
    'class' => \LFM\Lfmtheme\Form\Element\SelectBackendLayoutElement::class,
    'nodeName' => 'selectBackendLayout',
    'priority' => 40,
];
