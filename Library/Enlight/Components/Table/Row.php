<?php
/**
 * Enlight Db Table Row
 */
class Enlight_Components_Table_Row extends Zend_Db_Table_Row implements Enlight_Hook
{
	/**
     * _getTableFromString
     *
     * @param string $tableName
     * @return Zend_Db_Table_Abstract
     */
	protected function _getTableFromString($tableName)
    {
    	$tableName = Enlight_Class::getClassName($tableName);
    	return parent::_getTableFromString($tableName);
    }
}