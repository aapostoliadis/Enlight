<?php
class Default_Controllers_Backend_Auth extends Enlight_Controller_Action
{
    public function init()
    {
        $this->Front()->Plugins()->ScriptRenderer()->setRender();
    }

	public function preDispatch()
	{
        $this->View()->setCaching(true);
	}
	
	public function indexAction()
	{
	}

    public function loadAction()
    {
    }

    /**
     * Returns a JSON string from all registered backend users
     *
     * @return void
     */
    public function getUsersAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        echo '{"success":true,"data":[{"id":"48","username":"demo","password":"84c2ef7bb215395c80119636233765f0","sessionID":"ccbco9tagg7gipap85j8rqvnb6","lastlogin":"2011-12-29 12:36:47","name":"Administrator","email":"info@shopware.de","active":"1","sidebar":"1","window_height":"0","window_width":"0","window_size":"a:8:{s:12:\"articlesfast\";a:1:{i:1920;a:2:{s:6:\"height\";i:540;s:5:\"width\";i:1670;}}s:10:\"presetting\";a:2:{i:1920;a:2:{s:6:\"height\";i:575;s:5:\"width\";i:795;}i:1364;a:2:{s:6:\"height\";i:420;s:5:\"width\";i:845;}}s:8:\"articles\";a:1:{i:1920;a:2:{s:6:\"height\";i:665;s:5:\"width\";i:1225;}}s:8:\"shipping\";a:1:{i:1920;a:2:{s:6:\"height\";i:705;s:5:\"width\";i:1675;}}s:6:\"plugin\";a:1:{i:1920;a:2:{s:6:\"height\";i:755;s:5:\"width\";i:1395;}}s:9:\"orderlist\";a:1:{i:1920;a:2:{s:6:\"height\";i:585;s:5:\"width\";i:1740;}}s:3:\"rss\";a:1:{i:1920;a:2:{s:6:\"height\";i:550;s:5:\"width\";i:1320;}}s:11:\"payment_eos\";a:1:{i:1920;a:2:{s:6:\"height\";i:435;s:5:\"width\";i:1325;}}}","admin":"1","rights":"","salted":"1","failedlogins":"0","lockeduntil":"0000-00-00 00:00:00"}],"total":1}';
    }
}