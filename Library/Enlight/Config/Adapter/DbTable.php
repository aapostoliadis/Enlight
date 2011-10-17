<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Config
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Config
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 */
 class Enlight_Config_Adapter_DbTable extends Enlight_Config_Adapter
{
    /**
     * The name column in the database table.
     *
     * @var null|string
     */
    protected $_nameColumn = 'name';

    /**
     * The value column in the database table.
     *
     * @var null|string
     */
    protected $_valueColumn = 'value';

    /**
     * The section column in the database table.
     *
     * @var null|string
     */
    protected $_sectionColumn = 'section';

    /**
     * The automatic serialization option value.
     *
     * @var bool
     */
    protected $_automaticSerialization = false;

    /**
     * The created column in the database table.
     *
     * @var null|string
     */
    protected $_createdColumn = 'created';

    /**
     * The created column in the database table.
     *
     * @var null|string
     */
    protected $_updatedColumn = 'updated';

    /**
     * Sets the options of an array.
     *
     * @param array $options
     * @return Enlight_Config_Adapter
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key=>$option) {
            switch ($key) {
                case 'nameColumn':
                case 'valueColumn':
                case 'sectionColumn':
                case 'createdColumn':
                case 'updatedColumn':
                    $this->{'_'.$key} = $option;
                    break;
                case 'automaticSerialization':
                    $this->{'_'.$key} = (bool) $option;
                    break;
                default:
                    break;
            }
        }
        return parent::setOptions($options);
    }

     /**
      * Reads a section from the data store.
      *
      * @param Enlight_Config $config
      * @return array
      */
    public function read(Enlight_Config $config)
    {
        $name = $this->_namePrefix . $config->getName() . $this->_nameSuffix;

        $dbTable = new Enlight_Components_Table($name);
        $select = $dbTable->select()->from($dbTable->info('name'), array($this->_nameColumn, $this->_valueColumn));

        if(!empty($this->_sectionColumn)) {
            if(is_array($this->_sectionColumn)) {
                foreach ($this->_sectionColumn as $key=>$sectionColumn) {
                    if(isset($section[$key])) {
                        $select->where($sectionColumn.'=?', $section[$key]);
                    }
                }
            } elseif($section!==null) {
                $select->where($this->_sectionColumn.'=?', $section);
            }
        }

        if($this->_valueColumn !== '*') {
            $data = $dbTable->getAdapter()->fetchPairs($select);
        } else  {
            $data = $dbTable->getAdapter()->fetchAssoc($select);
        }

        if($this->_automaticSerialization) {
            foreach ($data as $key=>$value) {
                $data[$key] = unserialize($value);
            }
        }
        $config->setData($data);
    }

     /**
      * Saves the data changes in the data store.
      *
      * @param Enlight_Config $config
      * @param array $fields
      * @param bool $update
      * @return bool
      */
    public function write(Enlight_Config $config, $fields=null, $update=true)
    {
        $name = $this->_namePrefix . $config->getName() . $this->_nameSuffix;

        $dbTable = new Enlight_Components_Table($name);
        $db = $dbTable->getAdapter();

        $section = $config->getSectionName();

        if($fields===null) {
            $fields = $config->getDirtyFields();
        }
        if(empty($fields)) {
            return $this;
        }

        $updateData = array();
        $insertData = array();

        if($this->_updatedColumn!==null) {
            $updateData[$this->_updatedColumn] = new Zend_Db_Expr('NOW()');
            $insertData[$this->_updatedColumn] = new Zend_Db_Expr('NOW()');
        }
        if($this->_createdColumn!==null) {
            $insertData[$this->_createdColumn] = new Zend_Db_Expr('NOW()');
        }

        $where = array();
        if(is_array($this->_sectionColumn)) {
            foreach ($this->_sectionColumn as $key=>$sectionColumn) {
                if(isset($section[$key])) {
                    $where[] = $db->quoteInto($sectionColumn . '=?', $section[$key]);
                    $insertData[$sectionColumn] = $section[$key];
                }
            }
        } elseif($section !== null) {
            $where[] = $db->quoteInto($this->_sectionColumn . '=?', $section);
            $insertData[$this->_sectionColumn] = $section;
        }

        foreach ((array) $fields as $field) {
            $fieldWhere = $where;
            $fieldWhere[] = $db->quoteInto($this->_nameColumn.'=?', $field);

            $row = $dbTable->fetchRow($fieldWhere);

            if($row!==null) {
                if($update) {
                    $data = $updateData;
                    if ($this->_automaticSerialization){
                        $data[$this->_valueColumn] = serialize($config->get($field));
                    } else {
                        $data[$this->_valueColumn] = $config->get($field);
                    }
                    $dbTable->update($data, $fieldWhere);
                }
            } else {
                $data = $insertData;
                $data[$this->_nameColumn] = $field;
                if ($this->_automaticSerialization){
                    $data[$this->_valueColumn] = serialize($config->get($field));
                } else {
                    $data[$this->_valueColumn] = $config->get($field);
                }
                $dbTable->insert($data);
            }
        }
        $config->setDirtyFields(array_diff($config->getDirtyFields(), $fields));

        return $this;
    }
}