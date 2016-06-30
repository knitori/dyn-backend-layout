<?php
namespace LFM\Lfmtheme\Form\FormDataProvider;

/**
 * Resolve select items, set processed item list in processedTca, sanitize and resolve database field
 */
class TcaSelectItems extends \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems
{
    protected function processSelectFieldValue(array $result, $fieldName, array $staticValues)
    {
        if ($fieldName != 'lfm_row_selection') {
            return parent::processSelectFieldValue($result, $fieldName, $staticValues);
        }
        $currentDatabaseValueArray = array_key_exists($fieldName, $result['databaseRow']) ? $result['databaseRow'][$fieldName] : [];
        $newDatabaseValueArray = [];

        // Add all values that were defined by static methods and do not come from the relation
        // e.g. TCA, TSconfig, itemProcFunc etc.
        foreach ($currentDatabaseValueArray as $value) {
            if (isset($staticValues[$value])) {
                $newDatabaseValueArray[] = $value;
            }
        }

        return $newDatabaseValueArray;
    }
}
