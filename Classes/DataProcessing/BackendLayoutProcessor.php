<?php
/**
 * Created by PhpStorm.
 * User: Lars_Soendergaard
 * Date: 29.06.2016
 * Time: 11:59
 */

namespace LFM\Lfmtheme\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class BackendLayoutProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ) {
        $layoutField = $cObj->stdWrapValue('layoutField', $processorConfiguration, 'lfm_row_selection');
        $asField = $cObj->stdWrapValue('as', $processorConfiguration, 'backendLayouts');

        if (!isset($processedData['data'][$layoutField]) || !$processedData['data'][$layoutField]) {
            return $processedData;
        }
        $layoutFiles = GeneralUtility::trimExplode(',', $processedData['data'][$layoutField]);
        $baseDir = GeneralUtility::getFileAbsFileName('EXT:lfmtheme/Configuration/TSConfig/Puzzle/');
        $files = [];
        foreach ($layoutFiles as $filename) {
            $path = $baseDir.$filename;
            if (!file_exists($path)) {
                continue;
            }
            $files[] = $path;
        }
        $config = \LFM\Lfmtheme\Provider\SelectBackendLayoutDataProvider::buildConfigFromAllFiles($files);
        $processedData[$asField] = $config;

        return $processedData;
    }
}
