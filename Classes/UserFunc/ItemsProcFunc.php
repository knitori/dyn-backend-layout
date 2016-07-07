<?php
namespace LFM\Lfmtheme\UserFunc;

use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $data['items'] = [];
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

            /** @var TypoScriptParser $parser */
            $parser = GeneralUtility::makeInstance(TypoScriptParser::class);
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
