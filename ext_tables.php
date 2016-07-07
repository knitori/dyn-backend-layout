<?php
defined('TYPO3_MODE') or die();

call_user_func(function($extKey){

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_lfmtheme_domain_model_backendlayout');

}, $_EXTKEY);
