<?php
interface Enlight_Event_EventSubscriber
{
    /**
     * Returns an array of events that this subscriber listens 
     *
     * @return array
     */
    public function getSubscribedEvents();
}