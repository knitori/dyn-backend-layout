<?php
namespace LFM\Lfmtheme\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
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
class FlexLayoutElement extends AbstractFormElement
{
    /**
     * Render side by side element.
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
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
        ]);

        $html = $view->render();

        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $cssFile = ExtensionManagementUtility::extRelPath('lfmtheme') . 'Resources/Public/Css/flexlayout.css';
        $pageRenderer->addCssFile($cssFile);

        $resultArray = $this->initializeResultArray();
        $resultArray['requireJsModules'] = ['TYPO3/CMS/Lfmtheme/EditFlexLayout'];
        $resultArray['html'] = $html;
        return $resultArray;
    }

}