<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Cron
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     $Author$
 */

/**
 * This adapter allows you to implement cron tasks in your application.
 * The adapter handles all action between the controller and database.
 *
 * @category   Enlight
 * @package    Enlight_Cron
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */

class Enlight_Components_Cron_Adapter_DbTable extends Zend_Db_Table_Abstract implements Enlight_Components_Cron_Adapter
{
    /**
     * Database table name. Here are the Jobs are stored
     *
     * @var string
     */
    protected $_name = 'crontab';

    /**
     * Primary Key used for delete and update actions
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Name mapping between what the app and the real database.
     * Knows following Columns
     * -id - An integer field; unique; Primary Kex
     * -name - String(255); Name or descriptions of the Job eg. Check Stock
     * -action - Unique string(255) field. action becomes the event name during the execution of the job
     * -next - datetime; stores the date when the next run is due
     * -start - datetime; stores the last date when the job has been called
     * -end - datetime; stores the date/time an which the job stopped
     * -interval - Integer field; Stores the delta time between two runs in seconds.
     * -active - boolean field; 1 = active 0 = inactive.
     *
     * @var array
     */
    protected $_columns = array(
        'id' => 'id',
        'name' => 'name',
        'action' => 'action',
        'data' => 'data',
        'next' => 'next',
        'start' => 'start',
        'interval' => 'interval',
        'active' => 'active',
        'end' => 'end'
    );

    /**
     * Constructor - This class is derived from Zend_Db_Table.
     * An array of options can be provided to setup the object. The array should include a mapping
     * of database columns.
     * -idColumn - An integer field; unique; Primary Kex
     * -nameColumn - String(255); Name or descriptions of the Job eg. Check Stock
     * -actionColumn - Unique string(255) field. action becomes the event name during the execution of the job
     * -nextColumn - datetime; stores the date when the next run is due
     * -startColumn - datetime; stores the last date when the job has been called
     * -endColumn - datetime; stores the date/time an which the job stopped
     * -intervalColumn - Integer field; Stores the delta time between two runs in seconds.
     * -activeColumn - boolean field; 1 = active 0 = inactive.
     *
     * @param array|null $options
     * @see http://framework.zend.com/manual/en/zend.db.table.html
     */
    public function __construct(Array $options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * The options for this class will be set through this method
     * Every key of the array which ends with Column will be used as mapping indicator
     * every other option will be given over to the parent class.
     *
     * @param array $options
     * @return Zend_Db_Table_Abstract
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $option) {
            if (substr($key, -6) == 'Column') {
                $this->_columns[substr($key, 0, -6)] = (string)$option;
            }
        }
        return parent::setOptions($options);
    }

    /**
     * Writes the Job object to the database. If an ID is provided this method will try to update a data set. If there
     * is no ID given, the system will create a new data set.
     *
     * @param Enlight_Components_Cron_Job $job
     * @return Enlight_Components_Cron_Adapter_Adapter
     */
    public function write(Enlight_Components_Cron_Job $job)
    {
        $data = array();
        $where = null;
        foreach ($this->_columns as $key => $value) {
            switch ($key) {
                case 'data':
                    $data[$key] = serialize($job->$key);
                    break;
                default:
                    $data[$key] = $job->$key;
            }
        }

        if (is_null($job->getId())) {
            $next = $job->getNext();
            if (empty($next)) {
                $job->setNext(new Zend_Date());
            }
            $start = $job->getStart();
            if (empty($start)) {
                $zd = new Zend_Date();
                $job->setStart($zd->subDay(1));
            }
            $end = $job->getEnd();
            if (empty($end)) {
                $zd = new Zend_Date();
                $job->setEnd($zd->subDay(1));
            }
            $this->insert($data);
        } else {
            $where = $this->getAdapter()->quoteInto(
                $this->getAdapter()->quoteIdentifier($this->_primary) . ' = ?',
                $job->getId()
            );
            $this->update($data, $where);
        }
        return $this;
    }

    /**
     * Adds a job to the crontab.
     *
     * @param Enlight_Components_Cron_Job $job
     * @return Enlight_Components_Cron_Adapter_Adapter
     */
    public function addJob(Enlight_Components_Cron_Job $job)
    {
        if (!is_null($job->getId())) {
            throw new Enlight_Exception('Can not insert new job because an ID has been given.');
        }
        return $this->write($job);
    }

    /**
     * Updates a cron job in the cron tab
     *
     * @param Enlight_Components_Cron_Job $job
     * @return Enlight_Components_Cron_Adapter_Adapter
     */
    public function updateJob(Enlight_Components_Cron_Job $job)
    {
        if (is_null($job->getId())) {
            throw new Enlight_Exception('Can not update job because no ID has been given.');
        }
        return $this->write($job);
    }

    /**
     * Returns an array of Enlight_Components_Cron_Job from the crontab
     * If no cron jobs found the method will return an empty array
     *
     * @param bool $ignoreActive if set true the active flag will be ignored
     * @return array
     */
    public function getAllJobs($ignoreActive = false)
    {
        $where = null;
        if (!$ignoreActive) {
            #$where = $this->getAdapter()->quote($this->_columns['active'].' = 1');
            $where = $this->getAdapter()->quoteInto(
                $this->getAdapter()->quoteIdentifier($this->_columns['active']) . ' = ?',
                1
            );
        }
        $rows = $this->fetchAll($where);
        if (count($rows) === 0) {
            return array();
        }
        $retVal = array();
        foreach ($rows as $row) {
            $row['next'] = new Zend_Date($row['next']);
            $row['start'] = new Zend_Date($row['start']);
            $row['end'] = new Zend_Date($row['end']);
            $row['data'] = unserialize($row['data']);
            $retVal[$row['id']] = new Enlight_Components_Cron_Job($row->toArray());
        }
        return $retVal;
    }

    /**
     * Returns the next cron job based on the next date field
     *
     * @return null|Enlight_Components_Cron_Job
     */
    public function getNextJob()
    {
        $sql = new Zend_Db_Table_Select($this);
        $sql->where($this->getAdapter()->quoteIdentifier($this->_columns['active']), 1)
            ->where($this->getAdapter()->quoteIdentifier($this->_columns['end']), ' IS NOT NULL')
            ->where($this->getAdapter()->quoteIdentifier($this->_columns['next']), new Zend_Date());

        $row = $this->fetchRow($sql);
        if (count($row) === 0) {
            return null;
        }

        $row['next'] = new Zend_Date($row['next']);
        $row['start'] = new Zend_Date($row['start']);
        $row['end'] = new Zend_Date($row['end']);
        $row['data'] = unserialize($row['data']);
        $retVal = new Enlight_Components_Cron_Job($row->toArray());

        return $retVal;
    }

    /**
     * Internal helper method to grep data based on a given column name.
     *
     * @param $column
     * @param $value
     * @return Enlight_Components_Cron_Job|null
     */
    protected function getJobByColumn($column, $value)
    {
        $column = (string)$column;
        $value = (string)$value;
        $where = $this->getAdapter()->quoteInto($this->getAdapter()->quoteIdentifier($column) . ' = ?', $value);

        $row = $this->fetchRow($where);
        if (count($row) === 0) {
            return null;
        }

        $row['next'] = new Zend_Date($row['next']);
        $row['start'] = new Zend_Date($row['start']);
        $row['end'] = new Zend_Date($row['end']);
        $row['data'] = unserialize($row['data']);
        $retVal = new Enlight_Components_Cron_Job($row->toArray());

        return $retVal;
    }

    /**
     * Receives a single Cron job defined by its id from the crontab
     *
     * @param Int $id
     * @return Enlight_Components_Cron_Job|null
     */
    public function getJobById($id)
    {
        return $this->getJobByColumn($this->_columns['id'], $id);
    }

    /**
     * Returns a single job by its defined action
     *
     * @param $actionName
     * @return Enlight_Components_Cron_Adapter_Adapter|null
     */
    public function getJobByAction($actionName)
    {
        return $this->getJobByColumn($this->_columns['action'], $actionName);
    }

    /**
     * Returns a single cron job by its name
     *
     * @param String $name
     * @return Enlight_Components_Cron_Job
     */
    public function getJobByName($name)
    {
        return $this->getJobByColumn($this->_columns['name'], $name);
    }

    /**
     * Removes an job from the crontab
     *
     * @param Enlight_Components_Cron_Job $job
     * @return \Enlight_Components_Cron_Adapter_Adapter
     */
    public function removeJob(Enlight_Components_Cron_Job $job)
    {
        $where = $this->getAdapter()->quoteInto(
            $this->getAdapter()->quoteIdentifier($this->_primary) . ' = ?',
            $job->getId()
        );
        $this->delete($where);

        return $this;
    }
}