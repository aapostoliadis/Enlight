<?php
interface Enlight_View_ViewCache extends Enlight_View_View
{
	public function setCaching($value = true);
	public function isCached();
	public function setCacheID($cache_id = null);
}