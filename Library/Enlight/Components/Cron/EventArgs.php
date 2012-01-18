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
 * @category   Enlight
 * @package    Enlight_Cron
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Components_Cron_EventArgs extends Enlight_Event_EventArgs
{
    /** @var Enlight_Components_Cron_Job */
    protected $job;

    /**
     * Standard constructor method, the cron job are required.
     * If the job data property is a string, it will be unserialized.
     * @param Enlight_Components_Cron_Job $job
     */
    public function __construct(Enlight_Components_Cron_Job $job)
    {
        $data = $job->getData();
        if (is_string($data)) {
            $data = unserialize($data);
        }
        $this->job = $job;
        parent::__construct($job->getAction(), $data);
    }

    /**
     * Returns the Enlight_Components_Cron_Job
     *
     * @return Enlight_Components_Cron_Job
     */
    public function Job()
    {
        return $this->job;
    }
//    public function notifyUntil(Enlight_Components_Cron_EventArgs $args)
//    {
//        return parent::notifyUntil($args);
//    }
}
