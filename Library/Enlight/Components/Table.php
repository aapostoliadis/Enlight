<?php
/**
 * Enlight Db Table
 */
class Enlight_Components_Table extends Zend_Db_Table implements Enlight_Hook, Enlight_Singleton
{
	/**
     * Returns a normalized version of the reference map
     *
     * @return array
     */
    protected function _getReferenceMapNormalized()
    {
    	$maps = parent::_getReferenceMapNormalized();
    	foreach ($maps as $rule => $map) {
    		if(isset($map[self::REF_TABLE_CLASS])) {
    			$maps[$rule][self::REF_TABLE_CLASS] = Enlight_Class::getClassName($map[self::REF_TABLE_CLASS]);
    		}
    	}
    	return $maps;
    }
}