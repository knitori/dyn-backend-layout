<?php


// replace default TcaSelectItems data provider
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\LFM\Lfmtheme\Form\FormDataProvider\TcaSelectItems::class]
    = $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems::class];
unset($GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems::class]);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutDataProvider']['select']
    = \LFM\Lfmtheme\Provider\SelectBackendLayoutDataProvider::class;
