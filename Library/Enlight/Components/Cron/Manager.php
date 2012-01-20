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
 * The Enlight_Components_Cron_Manager managed all cron jobs. It iterate all cron jobs,
 * reads the events of the cron job adapters and execute them.
 *
 * @category   Enlight
 * @package    Enlight_Cron
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */

class Enlight_Components_Cron_Manager
{
    /**
     * @var Enlight_Components_Cron_Adapter
     */
    private $_adapter = null;

    /**
     * @var Enlight_Event_EventManager
     */
    private $_eventManager = null;

    /**
     * Constructor can be injected with a read / write adapter object
     *
     * @param Enlight_Components_Cron_Adapter $adapter
     * @param Enlight_Event_EventManager|null $eventManager
     * @return Enlight_Components_Cron_Manager
     */
    public function __construct(Enlight_Components_Cron_Adapter $adapter,
                                 Enlight_Event_EventManager $eventManager = null
    )
    {
        $this->setAdapter($adapter);
        if (is_null($eventManager)) {
            $eventManager = Enlight_Application::Instance()->Events();
        }
        $this->setEventManager($eventManager);
    }

    /**
     * Sets the read / write adapter
     *
     * @param Enlight_Components_Cron_Adapter $adapter
     * @return Enlight_Components_Cron_Manager
     */
    public function setAdapter(Enlight_Components_Cron_Adapter $adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Returns the read / write adapter
     *
     * @return Enlight_Components_Cron_Adapter|null
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Sets an Event Manager. Needed to execute the cron
     *
     * @param Enlight_Event_EventManager|null $eventManager
     * @return Enlight_Components_Cron_Manager
     */
    public function setEventManager(Enlight_Event_EventManager $eventManager = null)
    {
        $this->_eventManager = $eventManager;
        return $this;
    }

    /**
     * Returns the value set by setEventManager()
     *
     * @return Enlight_Event_EventManager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * Deactivate a given Cron Job in the crontab
     *
     * @param Enlight_Components_Cron_Job $job
     * @return Enlight_Components_Cron_Adapter_Adapter
     */
    public function disableJob(Enlight_Components_Cron_Job $job)
    {
        $job->setActive(false);
        return $this->_adapter->updateJob($job);
    }

    /**
     * Deactivate a given Cron Job
     *
     * @throws Enlight_Exception
     * @param \Enlight_Components_Cron_Job $job
     * @return Enlight_Components_Cron_Manager
     */
    public function removeJob(Enlight_Components_Cron_Job $job)
    {
        $this->_adapter->removeJob($job);
        return $this;
    }

    /**
     * Updates a cron job
     *
     * @throws Enlight_Exception
     * @param \Enlight_Components_Cron_Job $job
     * @return Enlight_Components_Cron_Manager
     */
    public function updateJob(Enlight_Components_Cron_Job $job)
    {
        $this->_adapter->updateJob($job);
        return $this;
    }

    /**
     * Returns an array of Enlight_Components_Cron_Job from crontab
     *
     * @return null|array of Enlight_Components_Cron_Job
     */
    public function getAllJobs()
    {
        return $this->_adapter->getAllJobs();
    }

    /**
     * Receives a single Cron job defined by its id from crontab
     *
     * @param Int $id
     * @return null|Enlight_Components_Cron_Job
     */
    public function getJobById($id)
    {
        $retVal = $this->_adapter->getJobById((int)$id);
        if (empty($retVal)) {
            return null;
        }
        return $retVal;
    }

    /**
     * Receives a single cron job by its name from the crontab
     *
     * @param String $name
     * @return null|Enlight_Components_Cron_Job
     */
    public function getJobByName($name)
    {
        $retVal = $this->_adapter->getJobByName((string)$name);
        if (empty($retVal)) {
            return null;
        }
        return $retVal;
    }

    /**
     * Adds an job to the crontab
     *
     * @param Enlight_Components_Cron_Job $job
     * @return Enlight_Components_Cron_Manager
     */
    public function addJob(Enlight_Components_Cron_Job $job)
    {
        $this->_adapter->addJob($job);
        return $this;
    }

    /**
     * Returns the next cron job who is due to execute
     *
     * @return null|Enlight_Components_Cron_Job
     */
    public function getNextJob()
    {
        $retVal = $this->_adapter->getNextJob();
        if (empty($retVal)) {
            return null;
        }
        return $retVal;
    }

    /**
     * Runs a job by handing it over to
     *
     * @param Enlight_Components_Cron_Job $job
     * @return Enlight_Event_EventArgs
     * @throw Enlight_Exception
     */
    public function run(Enlight_Components_Cron_Job $job)
    {
        try {
            if ($this->startJob($job)) {
                $jobArgs = $this->_eventManager->notifyUntil(
                    $job->getAction(),
                    new Enlight_Components_Cron_EventArgs($job)
                );
                $this->endJob($job);
                return $jobArgs;
            }
        }
        catch (Exception $e) {
            $job->setData((array('error' => $e->getMessage())));
            $this->disableJob($job);
            throw $e;
        }
    }

    /**
     * Ends a job by handing it over to
     *
     * @param Enlight_Components_Cron_Job $job
     * @return void
     */
    private function endJob(Enlight_Components_Cron_Job $job)
    {
        $job->setEnd(new Zend_Date());
        $this->_adapter->updateJob($job);
    }

    /**
     * Starts a job by handing it over to
     *
     * @param Enlight_Components_Cron_Job $job
     * @return void|bool
     */
    private function startJob(Enlight_Components_Cron_Job $job)
    {
        $nextRun = $job->getNext(); // get next Date

        // Turn clock forward
        do {
            $nextRun->addSecond($job->getInterval());
        } while ($nextRun->compare(new Zend_Date()) >= 0);

        $job->setStart(new Zend_Date());
        $job->setEnd(null);

        try {
            $this->_adapter->updateJob($job);
            $job->setNext($nextRun);
            return true;
        }
        catch (Exception $e) {
            return false;
        }
    }
}
