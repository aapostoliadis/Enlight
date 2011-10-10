<?php
interface Enlight_Hook_HookSubscriber
{
    /**
     * Returns an array of events that this subscriber listens 
     *
     * @return array
     */
    public function getSubscribedHooks();
}