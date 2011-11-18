<?php
class Enlight_View_ViewDefault extends Enlight_Class implements Enlight_View_ViewCache
{
    /**
     * Smarty object
     * 
     * @var     Enlight_Template_Manager
     */
    protected $engine;
    
    /**
     * Enter description here...
     *
     * @var     Enlight_Template_Handler
     */
    protected $template;
    
    /**
     * Enter description here...
     *
     * @var     Smarty_Internal_Data
     */
    protected $data;
    
    protected function init()
    {
    	$this->resolveTemplateEngine();
    }

    /**
     * @return Enlight_Template_Manager
     */
    public function Engine()
    {
    	return $this->engine;
    }

    /**
     * @return Enlight_Template_Handler
     */
    public function Template()
    {
    	return $this->template;
    }
    
    protected function resolveTemplateEngine()
	{
		if($this->engine === null) {
			$this->setTemplateEngine(Enlight_Application::Instance()->Bootstrap()->getResource('Template'));
		}
	}
  
    /**
     * Setze den Pfad zu den Templates
     *
     * @param string $path Das Verzeichnis, das als Pfad gesetzt werden soll.
     * @return void
     */
    public function setTemplateDir($path)
    {
    	$this->engine->setTemplateDir($path);
    }
    
    /**
     * Setze den Pfad zu den Templates
     *
     * @param string $path Das Verzeichnis, das als Pfad gesetzt werden soll.
     * @return void
     */
    public function addTemplateDir($path)
    {
    	$this->engine->addTemplateDir($path);
    }
    
    public function loadTemplate($template_name)
    {
    	$this->template = $this->engine->createTemplate($template_name, $this->data);
    }
    
    public function createTemplate($template_name)
    {
    	return $this->engine->createTemplate($template_name, $this->data);
    }
    
    public function extendsTemplate($template_name)
    {
    	if($this->template) {
    		$this->template->extendsTemplate($template_name);
    	}
		return $this;
    }
    
    public function extendsBlock($spec, $content, $mode = 'replace')
    {
    	if($this->template) {
    		$this->template->extendsBlock($spec, $content, $mode);
    	}
		return $this;
    }
    
    public function setTemplateEngine($engine)
    {
    	$this->engine = $engine;
    	$this->data = $this->engine->createData($this->engine);
    }
    
    public function setTemplate($template=null)
    {
    	$this->template = $template;
    }
    
    public function hasTemplate()
    {
    	return isset($this->template);
    }
    
    public function templateExists($template_name)
    {
    	return $this->engine->templateExists($template_name);
    }

    public function assign($spec, $value = null, $nocache = null, $scope = null)
    {
    	if($nocache===null) {
    		$nocache = $this->noCache;
    	}
    	$this->data->assign($spec, $value, $nocache, $scope);
    }
	
    public function clearAssign($spec = null)
    {
    	if(isset($spec)) {
        	$this->data->clearAllAssign();
        } else {
        	$this->data->clearAssign($spec);
        }
        return $this;
    }

    /**
     * @param   string|null $spec
     * @return  mixed|array
     */
    public function getAssign($spec = null)
    {
    	return $this->data->getTemplateVars($spec);
    }

    /**
     * Verarbeitet ein Template und gibt die Ausgabe zurück
     *
     * @return string
     */
    public function render()
    {
    	return $this->template->fetch();
    }
    
    public function fetch($template_name)
    {
    	$_template = $this->engine->createTemplate($template_name, $this->data);
		return $_template->fetch();
    }
    
    protected $noCache = false;
    
    public function setNoCache($value=true)
    {
    	$this->noCache = (bool) $value;
    }
    
    public function setCaching($value=true)
    {
    	$this->engine->setCaching($value);
    	if($this->template!==null) {
    		$this->template->setCaching($value);
    	}
    }
    
    public function isCached()
    {
    	return $this->template ? $this->template->isCached() : false;
    }
    
    public function setCacheID($cache_id = null)
    {
    	if(!$this->template) {
    		return $this;
    	}
    	if(is_array($cache_id)) {
    		$cache_id = implode('|', $cache_id);
    	}
    	$this->template->cache_id = (string) $cache_id;
    	return $this;
    }
    
    public function addCacheID($cache_id)
    {
    	if(!$this->template) {
    		return $this;
    	}
    	if(is_array($cache_id)) {
    		$cache_id = implode('|', $cache_id);
    	} else {
            $cache_id = (string) $cache_id;
        }
    	if(empty($this->template->cache_id)) {
    		$this->template->cache_id = $cache_id;
    	} else {
    		$this->template->cache_id .= '|' . $cache_id;
    	}
    	return $this;
    }
    
    public function clearCache($template = null, $cache_id = null, $compile_id = null, $exp_time = null, $type = null)
    {
    	return $this->engine->clearCache($template, $cache_id, $compile_id, $exp_time, $type);
    }
    
    public function clearAllCache($exp_time = null)
    {
    	$this->engine->clearAllCache($exp_time);
    }
        
    public function __set($name, $value=null)
    {
        $this->assign($name, $value);
    }

    public function __get($name)
    {
        return $this->getAssign($name);
    }

    public function __isset($name)
    {
        return ($this->getAssign($name) !== null);
    }

    public function __unset($name)
    {
        $this->clearAssign($name);
    }
}