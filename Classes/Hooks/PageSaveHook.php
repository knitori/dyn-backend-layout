<?php
/**
 * Created by PhpStorm.
 * User: Lars_Soendergaard
 * Date: 07.07.2016
 * Time: 10:51
 */

namespace LFM\Lfmtheme\Hooks;

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class PageSaveHook
{
    /**
     * @var \LFM\Lfmtheme\Domain\Repository\BackendLayoutRepository
     * @inject
     */
    protected $backendLayoutRepository;


    function processDatamap_postProcessFieldArray($status, $table, $pageId, &$fieldArray, &$pObj) {
        if ($table == 'pages' && $status == 'update') {
            DebuggerUtility::var_dump($fieldArray);
            if (!isset($fieldArray['lfm_row_selection'])) {
                return;
            }
            $layoutFiles = GeneralUtility::trimExplode(',', $fieldArray['lfm_row_selection'], true);
            DebuggerUtility::var_dump($layoutFiles);

            /** @var DatabaseConnection $db */
            $db = $GLOBALS['TYPO3_DB'];
            $layoutTable = 'tx_lfmtheme_domain_model_backendlayout';

            $rows = $db->exec_SELECTgetRows(
                '*',
                $layoutTable,
                'NOT deleted AND pid='.$pageId,
                '',
                'layout_pos');

            $usedRelColPos = [];
            $dbLayouts = [];
            $deletedUids = [];
            foreach($rows as $row) {
                $dbLayouts[$row['uid']] = $row;
                $deletedUids[$row['uid']] = $row;
                $usedRelColPos[] = intval($row['rel_col_pos']);
            }

            $data = [];
            $data[$layoutTable] = [];

            $baseDir = GeneralUtility::getFileAbsFileName('EXT:lfmtheme/Configuration/TSConfig/Puzzle/');
            $layouts = [];

            $position = 1;
            foreach ($layoutFiles as $filename) {
                if (MathUtility::canBeInterpretedAsInteger($filename)) {
                    $dbUid = intval($filename);
                    if (isset($dbLayouts[$dbUid])) {
                        $layouts[] = $dbUid;
                        unset($deletedUids[$dbUid]);
                        $data[$layoutTable][$dbUid] = array(
                            'layout_pos' => $position++,
                        );
                    }
                } else {
                    $path = $baseDir . $filename;
                    if (!file_exists($path)) {
                        continue;
                    }
                    $freeRelColPos = 0;
                    while (in_array($freeRelColPos, $usedRelColPos)) {
                        $freeRelColPos += 10000;
                    }
                    $usedRelColPos[] = $freeRelColPos;

                    /** @var TypoScriptParser $parser */
                    $parser = GeneralUtility::makeInstance(TypoScriptParser::class);
                    $parser->parse(file_get_contents($path));
                    $setup = $parser->setup;

                    $tmpUid = 'NEW'.($position+1);
                    $data[$layoutTable][$tmpUid] = [
                        'pid' => $pageId,
                        'title' => isset($setup['layout.']['title']) ? $setup['layout.']['title'] : $filename,
                        'rel_col_pos' => $freeRelColPos,
                        'layout_file' => $filename,
                        'layout_pos' => $position++,
                    ];
                    $layouts[] = $tmpUid;
                }
            }

            /** @var $tce DataHandler */
            $tce = GeneralUtility::makeInstance(DataHandler::class);
            $tce->bypassAccessCheckForRecords = true;
            $tce->start($data, []);
            $tce->admin = true;
            $tce->process_datamap();

            foreach ($layouts as $key => $uid) {
                if (isset($tce->substNEWwithIDs[$uid])) {
                    $layouts[$key] = $tce->substNEWwithIDs[$uid];
                }
            }
            $fieldArray['lfm_row_selection'] = implode(',', $layouts);


            $cmd = [];
            $cmd[$layoutTable] = [];
            foreach ($deletedUids as $uid => $row) {
                $cmd[$layoutTable][$uid] = [];
                $cmd[$layoutTable][$uid]['delete'] = 1;
            }

            /** @var $tce DataHandler */
            $tce = GeneralUtility::makeInstance(DataHandler::class);
            $tce->bypassAccessCheckForRecords = true;
            $tce->start([], $cmd);
            $tce->admin = true;
            $tce->process_cmdmap();
        }
    }
}

