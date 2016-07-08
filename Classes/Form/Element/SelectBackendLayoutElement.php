<?php
namespace LFM\Lfmtheme\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Render a widget with two boxes side by side.
 *
 * This is rendered for config type=select, maxitems > 1, renderType=selectMultipleSideBySide set
 */
class SelectBackendLayoutElement extends AbstractFormElement
{
    /**
     * Render side by side element.
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $layouts = [];

        $config = $this->data['parameterArray']['fieldConf']['config'];
        if (isset($config['layoutsPath'])) {
            $path = GeneralUtility::getFileAbsFileName($config['layoutsPath']);
            $layouts = $this->getLayouts($path);
        }

        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $base = 'EXT:lfmtheme/Resources/Private/Backend/';
        $view->setTemplateRootPaths([$base.'Templates']);
        $view->setLayoutRootPaths([$base.'Layouts']);
        $view->setPartialRootPaths([$base.'Partials']);
        $view->setTemplate('SelectBackendLayout');

        $view->assignMultiple([
            'tableName' => $this->data['tableName'],
            'fieldName' => $this->data['fieldName'],
            'availableLayouts' => $layouts,
        ]);

        $html = $view->render();

        $resultArray = $this->initializeResultArray();
        $resultArray['requireJsModules'] = ['TYPO3/CMS/Lfmtheme/Module'];
        $resultArray['html'] = $html;
        return $resultArray;
    }


    protected function getLayouts($path) {
        if (!file_exists($path) || !is_dir($path)) {
            return [];
        }
        $files = scandir($path);
        if (!is_array($files)) {
            return [];
        }
        $layouts = [];
        foreach ($files as $file) {
            $fullPath = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$file;
            $tmp = strtolower($file);
            if (substr($tmp, -3) != '.ts' && substr($tmp, -4) != '.txt') {
                continue;
            }

            /** @var TypoScriptParser $parser */
            $parser = GeneralUtility::makeInstance(TypoScriptParser::class);
            $parser->parse(file_get_contents($fullPath));
            $setup = $parser->setup;

            $layout = [
                'title' => isset($setup['layout.']['title']) ? $setup['layout.']['title'] : $file,
                'file' => $file,
                'path' => $fullPath,
            ];

            $layouts[] = $layout;

        }
        return $layouts;
    }

}