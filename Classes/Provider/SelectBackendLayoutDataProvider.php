<?php
/**
 * Created by PhpStorm.
 * User: Lars_Soendergaard
 * Date: 24.06.2016
 * Time: 12:37
 */

namespace LFM\Lfmtheme\Provider;

use TYPO3\CMS\Backend\View\BackendLayout\BackendLayout;
use TYPO3\CMS\Backend\View\BackendLayout\BackendLayoutCollection;
use TYPO3\CMS\Backend\View\BackendLayout\DataProviderContext;
use TYPO3\CMS\Backend\View\BackendLayout\DataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use LFM\Lfmtheme\Utility\HelperUtility;

class SelectBackendLayoutDataProvider implements DataProviderInterface
{
    public function addBackendLayouts(
        DataProviderContext $dataProviderContext,
        BackendLayoutCollection $backendLayoutCollection
    ) {
        $backendLayoutCollection->add($this->createBackendLayout($dataProviderContext->getData()));
    }

    public function getBackendLayout($identifier, $pageId)
    {
        /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $db */
        $db = $GLOBALS['TYPO3_DB'];
        $data = $db->exec_SELECTgetSingleRow('*', 'pages', 'uid='.$pageId);
        return $this->createBackendLayout($data);
    }

    protected function createBackendLayout($pageData)
    {
        $layout = BackendLayout::create(
            'selectTemplate',
            'Select',
            $this->getLayoutConfig($pageData)
        );
        $layout->setIconPath('EXT:lfmtheme/Resources/Public/Icons/lichtflut.png');
        return $layout;
    }

    /**
     * Create the backend layout tsconfig... It works, but I'm not sure though if
     * it's possible to set layout using arrays or anything like that, instead of
     * fugly tsconfig text.
     *
     * @param $pageData
     * @return string
     */
    protected function getLayoutConfig($pageData)
    {

        $baseDir = GeneralUtility::getFileAbsFileName('EXT:lfmtheme/Configuration/TSConfig/Puzzle/');
        $filenames = GeneralUtility::trimExplode(',', $pageData['lfm_row_selection']);
        $files = [];
        foreach ($filenames as $filename) {
            $path = $baseDir.$filename;
            if (!file_exists($path)) {
                continue;
            }
            $files[] = $path;
        }

        $layoutConfig = $this->buildConfigFromAllFiles($files);

        $layout = [];
        $layout[] = 'backend_layout {';
        $layout[] = "rowCount = {$layoutConfig['rowCount']}";
        $layout[] = "colCount = {$layoutConfig['colCount']}";
        $layout[] = 'rows {';

        $rowCounter = HelperUtility::Count(1);
        foreach ($layoutConfig['files'] as $fileConfig) {

            foreach (HelperUtility::Zip($rowCounter, $fileConfig['rows']) as list($rowNum, $row)) {
                $layout[] = "{$rowNum} {";
                $layout[] = 'columns {';

                foreach (HelperUtility::Enumerate($row, 1) as list($colNum, $column)) {
                    $layout[] = "{$colNum} {";
                    $layout[] = "name = {$column['name']}";
                    $layout[] = "colspan = {$column['colspan']}";
                    $layout[] = "colPos = {$column['colPos']}";
                    $layout[] = "}";
                }

                $layout[] = '}';  // columns
                $layout[] = '}';  // rowNum
            }
        }
        $layout[] = '}';  // rows
        $layout[] = '}';  // backend_layout

        return implode("\n", $layout);
    }


    static public function buildConfigFromAllFiles($files)
    {
        $layoutConfig = [
            'rowCount' => 0,
            'colCount' => 0,
            'files' => [],
        ];

        $totalRowCount = 0;
        $maxColCount = 0;

        $colPos = 0;
        foreach ($files as $path) {
            $fileConfig = [];
            $fileColPosColumns = [];

            $pathinfo = pathinfo($path);

            /** @var \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser $parser */
            $parser = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser::class);
            $parser->parse(file_get_contents($path));
            $setup = $parser->setup;

            $title = isset($setup['layout.']['title']) ? $setup['layout.']['title'] : $pathinfo['filename'];
            $rows = isset($setup['layout.']['rows.']) ? $setup['layout.']['rows.'] : [];
            $totalRowCount += count($rows);
            $fileConfig['rows'] = [];

            foreach ($rows as $row) {
                $columns = isset($row['columns.']) ? $row['columns.'] : [];
                $colCount = 0;

                $rowArray = [];
                foreach (HelperUtility::Enumerate($columns, 1) as list($colNum, $column)) {
                    $columnsArray = [];
                    $colspan = isset($column['colspan']) ? intval($column['colspan']) : 1;
                    $colCount += $colspan ? $colspan : 1;
                    $name = isset($column['name']) ? $column['name'] : "Spalte: {$colNum}";

                    $columnsArray['name'] = $title.": ".$name;
                    $active = isset($column['active']) ? $column['active'] : true;
                    if(in_array(strtolower($active), ['off', 'no', 'false', '0'])) {
                        $active = false;
                    }
                    if($active) {
                        $columnsArray['colPos'] = $colPos;
                        $fileColPosColumns[] = $colPos;
                        $colPos++;
                    }

                    if ($colspan) {
                        $columnsArray['colspan'] = $colspan;
                    }
                    $rowArray[] = $columnsArray;
                }
                $maxColCount = max($maxColCount, $colCount);

                $fileConfig['rows'][] = $rowArray;
            }
            $fileConfig['pathinfo'] = $pathinfo;
            $fileConfig['colPos'] = $fileColPosColumns;
            $layoutConfig['files'][] = $fileConfig;
        }

        $layoutConfig['rowCount'] = $totalRowCount;
        $layoutConfig['colCount'] = $maxColCount;

        $rowsTotal = [];

        foreach ($layoutConfig['files'] as $fileKey => $fileConfig) {
            foreach ($fileConfig['rows'] as $rowKey => $row) {
                $rowTotal = 0;
                foreach ($row as $column) {
                    $rowTotal += $column['colspan'];
                }
                $rowsTotal[] = $rowTotal;
            }
        }
        $rowsTotal = array_unique($rowsTotal);

        // least common multiplier
        $lcm = 1;
        while ($rowsTotal) {
            $lcm = HelperUtility::Lcm($lcm, array_pop($rowsTotal));
        }
        $layoutConfig['colCount'] = $lcm;

        // fix colspans to match the LCM.
        foreach ($layoutConfig['files'] as $fileKey => $fileConfig) {
            foreach ($fileConfig['rows'] as $rowKey => $row) {
                $rowTotal = 0;
                foreach ($row as $column) {
                    $rowTotal += $column['colspan'];
                }
                $multiplier = intval($lcm / $rowTotal);
                foreach ($row as $colKey => $column) {
                    $layoutConfig['files'][$fileKey]['rows'][$rowKey][$colKey]['colspan'] = $multiplier * $column['colspan'];
                }
            }
        }

        return $layoutConfig;
    }
}