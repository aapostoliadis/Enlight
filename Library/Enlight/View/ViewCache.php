<?php
interface Enlight_View_ViewCache extends Enlight_View_View
{
	public function setCaching($value=true);
	public function isCached();
	public function setCacheID($cache_id = null);
	public function clearCache($template = null, $cache_id = null, $compile_id = null, $exp_time = null, $type = null);
	public function clearAllCache($exp_time = null);
}