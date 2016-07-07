<?php
namespace LFM\Lfmtheme\UserFunc;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class ItemsProcFunc
{
    /**
     * Used to retrieve Backend layout "rows", that can be used to generate
     * one big backend layout dynamically.
     *
     * @param $data
     */
    public function getLayoutRows(&$data)
    {
        $baseDir = GeneralUtility::getFileAbsFileName('EXT:lfmtheme/Configuration/TSConfig/Puzzle/');
        $files = scandir($baseDir);
        foreach ($files as $file) {
            $path = $baseDir.$file;
            $pathinfo = pathinfo($path);
            if (GeneralUtility::isFirstPartOfStr($pathinfo['filename'], '.')) {
                continue;
            }
            if (!in_array(strtolower($pathinfo['extension']), ['ts', 'txt'])) {
                continue;
            }

            /** @var \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser $parser */
            $parser = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser::class);
            $parser->parse(file_get_contents($path));
            $setup = $parser->setup;
            $title = isset($setup['layout.']['title']) ? $setup['layout.']['title'] : $pathinfo['filename'];
            $data['items'][] = [
                $title,
                $file,
            ];
        }
    }

    /**
     * Used to retrieve Backend layout "rows", that can be used to generate
     * one big backend layout dynamically.
     *
     * @param $data
     */
    public function getLayoutRows(&$data)
    {
        $baseDir = GeneralUtility::getFileAbsFileName('EXT:lfmtheme/Configuration/TSConfig/Puzzle/');
        $files = scandir($baseDir);
        foreach ($files as $file) {
            $path = $baseDir.$file;
            $pathinfo = pathinfo($path);
            if (GeneralUtility::isFirstPartOfStr($pathinfo['filename'], '.')) {
                continue;
            }
            if (!in_array(strtolower($pathinfo['extension']), ['ts', 'txt'])) {
                continue;
            }

            /** @var \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser $parser */
            $parser = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser::class);
            $parser->parse(file_get_contents($path));
            $setup = $parser->setup;
            $title = isset($setup['layout.']['title']) ? $setup['layout.']['title'] : $pathinfo['filename'];
            $data['items'][] = [
                $title,
                $file,
            ];
        }
    }
}
