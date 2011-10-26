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
 * @package    Enlight_Extensions
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Extensions
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Extensions_Benchmark_Bootstrap extends Enlight_Plugin_Bootstrap_Config
{
    protected $results = array();
	protected $startTime = null;
	protected $startMemory = null;

	/**
	 * Activate template debugging
	 */
	public function init()
	{
		if(!$this->Application()->Bootstrap()->hasResource('Log')){
			return;
		}

		$this->Application()->Template()->setDebugging(true);
		$this->Application()->Template()->setDebugTemplate('string:');
		//$this->Application()->Events()->addSubscriber($this);
	}

	/**
	 * Install benchmark plugin
	 */
	public function install()
	{
		$event = $this->createEvent(
			'Enlight_Controller_Front_StartDispatch',
			'onStartDispatch'
		);
		$this->subscribeEvent($event);
		$event = $this->createEvent(
			'Enlight_Bootstrap_InitResource_Benchmark',
			'onInitResourceBenchmark'
		);
		$this->subscribeEvent($event);

		$event = $this->createEvent(
	 		'Enlight_Controller_Front_DispatchLoopShutdown',
	 		'onDispatchLoopShutdown'
	 	);
		$this->subscribeEvent($event);
	}

	/**
	 * On Dispatch start activate db profiling
	 *
	 * @param Enlight_Event_EventArgs $args
	 * @return void
	 */
	public function onStartDispatch(Enlight_Event_EventArgs $args)
    {
    	$this->Application()->Plugins()->Core()->Benchmark();
		$this->Application()->Db()->getProfiler()->setEnabled(true);
    }

	/**
	 * On Dispatch Shutdown collect sql performance results and dump to log component
	 * 
	 * @param Enlight_Event_EventArgs $args
	 * @return
	 */
	public function onDispatchLoopShutdown(Enlight_Event_EventArgs $args)
    {
		if(!$this->Application()->Bootstrap()->hasResource('Log')){
			return;
		}
		$profiler = $this->Application()->Db()->getProfiler();

        $rows = array(array('time','count','sql','params'));
		$counts = array(10000);
		$total_time = 0;
		$queryProfiles = $profiler->getQueryProfiles();

		if(!$queryProfiles) {
			return;
        }
        
		foreach ($queryProfiles as $query) {
			$id = md5($query->getQuery());
			$total_time += $query->getElapsedSecs();
			if(!isset($rows[$id])){
				$rows[$id] = array(
					number_format($query->getElapsedSecs(), 5, '.', ''),
					1,
					$query->getQuery(),
					$query->getQueryParams()
				);
				$counts[$id] = $query->getElapsedSecs();
			} else {
				$rows[$id][1]++;
				$counts[$id] += $query->getElapsedSecs();
				$rows[$id][0] = number_format($counts[$id], 5, '.', '');
			}
		}

		array_multisort($counts, SORT_NUMERIC, SORT_DESC, $rows);
		$rows = array_values($rows);
		$total_time = round($total_time, 5);
		$total_count = $profiler->getTotalNumQueries();
        
		$label = "Database Querys ($total_count @ $total_time sec)";
		$table = array($label,
			$rows
		);
		$this->Application()->Log()->table($table);
	}

	/**
	 * Benchmark Controllers
     *
	 * @param Enlight_Event_EventArgs $args
	 * @return void
	 */
	public function onBenchmarkEvent(Enlight_Event_EventArgs $args)
    {
    	if(empty($this->results)) {
    		$this->results[] = array('name', 'memory', 'time');
    		$this->startTime = microtime(true);
			$this->startMemory = memory_get_peak_usage(true);
    	}

    	$this->results[] = array(
    		0 => str_replace('Enlight_Controller_', '', $args->getName()),
    		1 => $this->formatMemory(memory_get_peak_usage(true)-$this->startMemory),
    		2 => $this->formatTime(microtime(true)-$this->startTime)
    	);

    	if($args->getName() == 'Enlight_Controller_Front_DispatchLoopShutdown') {
    		$this->logTemplate();
    		$this->logController();
    	}
    }

	/**
	 * Log template compile and render times
     *
	 * @return void
	 */
    public function logTemplate()
    {
    	$rows = array(array('name', 'compile_time', 'render_time', 'cache_time'));
		$total_time = 0;
		foreach (Smarty_Internal_Debug::$template_data as $template_file) {
			$total_time += $template_file['render_time'];
			$total_time += $template_file['cache_time'];
			$template_file['name'] = str_replace($this->Application()->CorePath(), '', $template_file['name']);
			$template_file['name'] = str_replace($this->Application()->AppPath(), '', $template_file['name']);
			$template_file['name'] = str_replace($this->Application()->OldPath(), '', $template_file['name']);
			$template_file['compile_time'] = $this->formatTime($template_file['compile_time']);
			$template_file['render_time'] = $this->formatTime($template_file['render_time']);
			$template_file['cache_time'] = $this->formatTime($template_file['cache_time']);
			unset($template_file['startTime']);
			$rows[] = array_values($template_file);
		}
		$total_time = round($total_time, 5);
		$total_count = count($rows)-1;
		$label = "Benchmark Template ($total_count @ $total_time sec)";
		$table = array($label,
			$rows
		);
		$this->Application()->Log()->table($table);
    }

	/**
	 * Get total execution time in controller
     *
	 * @return void
	 */
    public function logController()
    {
    	$total_time = $this->formatTime(microtime(true) - $this->startTime);
		$label = "Benchmark Controller ($total_time sec)";
		$table = array($label,
			$this->results
		);
		$this->Application()->Log()->table($table);
    }

	/**
	 * Monitor execution time and memory on specified event points in application
     *
	 * @return array
	 */
	public function getListeners()
    {
    	$events = array(
    		'Enlight_Controller_Front_RouteStartup',
			'Enlight_Controller_Front_RouteShutdown',
			'Enlight_Controller_Front_DispatchLoopStartup',
			'Enlight_Controller_Front_PreDispatch',
			'Enlight_Controller_Front_PostDispatch',
			'Enlight_Controller_Front_DispatchLoopShutdown',

			'Enlight_Controller_Action_Init',
			'Enlight_Controller_Action_PreDispatch',
			'Enlight_Controller_Action_PostDispatch',

			'Enlight_Plugins_ViewRenderer_PreRender',
			'Enlight_Plugins_ViewRenderer_PostRender'
    	);
    	$listeners = array();
    	foreach ($events as $event) {
    		$listeners[] = new Enlight_Event_Handler($event, array($this, 'onBenchmarkEvent'), -99);
    	}
    	return $listeners;
    }

	/**
	 * Format memory in a proper way
	 * 
	 * @param  $size
	 * @return string
	 */
    public static function formatMemory($size)
    {
    	if (empty($size)) {
            return '0.00 b';
        }
    	$unit = array('b','kb','mb','gb','tb','pb');
    	return @number_format($size / pow(1024, ($i = floor(log($size, 1024)))), 2, '.', '') . ' ' . $unit[$i];
    }

	/**
	 * Format time for human readable
	 *
	 * @param  $time
	 * @return string
	 */
    public static function formatTime($time)
    {
    	return number_format($time, 5, '.', '');
    }
}