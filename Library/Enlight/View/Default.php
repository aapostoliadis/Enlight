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
 * @package    Enlight_View
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_View
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_View_Default extends Enlight_View implements Enlight_View_Cache
{
    /**
     * The template manager instance.
     *
     * @var     Enlight_Template_Manager
     */
    protected $engine;

    /**
     * The loaded template instance.
     *
     * @var     Enlight_Template_Default
     */
    protected $template;

    /**
     * Default nocache flag.
     *
     * @var     bool
     */
    protected $nocache;

    /**
     * Default assign scope
     *
     * @var     int
     */
    protected $scope;

    /**
     * @param   Enlight_Template_Manager $engine
     */
    public function __construct(Enlight_Template_Manager $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return  Enlight_Template_Manager
     */
    public function Engine()
    {
        return $this->engine;
    }

    /**
     * @return  Enlight_Template_Default
     */
    public function Template()
    {
        return $this->template;
    }

    /**
     * Sets the template path list.
     *
     * @param   string|array $path
     * @return  Enlight_View_Default
     */
    public function setTemplateDir($path)
    {
        $this->engine->setTemplateDir($path);
        return $this;
    }

    /**
     * Adds a path to the template list.
     *
     * @param   string|array $path
     * @return  Enlight_View_Default
     */
    public function addTemplateDir($path)
    {
        $this->engine->addTemplateDir($path);
        return $this;
    }

    /**
     * Sets the current template instance.
     *
     * @param   Enlight_Template_Default $template
     * @return  Enlight_View_Default
     */
    public function setTemplate(Enlight_Template_Default $template = null)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Checks if a template is stored.
     *
     * @return  bool
     */
    public function hasTemplate()
    {
        return isset($this->template);
    }

    /**
     * Loads a template by name.
     *
     * @param   string $template_name
     * @return  Enlight_View_Default
     */
    public function loadTemplate($template_name)
    {
        $this->template = $this->engine->createTemplate($template_name, $this->engine);
        return $this;
    }

    /**
     * Creates a new template by name.
     *
     * @param   $template_name
     * @return  Enlight_Template_Default
     */
    public function createTemplate($template_name)
    {
        return $this->engine->createTemplate($template_name, $this->template);
    }

    /**
     * @param   $template_name
     * @return  Enlight_View_Default
     */
    public function extendsTemplate($template_name)
    {
        if ($this->template) {
            $this->template->extendsTemplate($template_name);
        }
        return $this;
    }

    /**
     * Extends a template block by name.
     *
     * @param          $spec
     * @param          $content
     * @param   string $mode
     * @return  Enlight_View_Default
     */
    public function extendsBlock($spec, $content, $mode)
    {
        $this->template->extendsBlock($spec, $content, $mode);
        return $this;
    }

    /**
     * @param   $template_name
     * @return  bool
     */
    public function templateExists($template_name)
    {
        return $this->engine->templateExists($template_name);
    }

    /**
     * Assigns a specified value to the template.
     *
     * @param   string $spec
     * @param   mixed  $value
     * @param   bool   $nocache
     * @param   int    $scope
     * @return  Enlight_View_Default
     */
    public function assign($spec, $value = null, $nocache = null, $scope = null)
    {
        if ($this->nocache !== null && $nocache === null) {
            $nocache = $this->nocache;
        }
        if ($this->scope !== null && $scope === null) {
            $scope = $this->scope;
        }
        $this->template->assign($spec, $value, $nocache, $scope);
        return $this;
    }

    /**
     * Resets a specified value or all values.
     *
     * @param   string $spec
     * @param null     $scope
     * @return  Enlight_View_Default
     */
    public function clearAssign($spec = null, $scope = null)
    {
        if ($this->scope !== null && $scope === null) {
            $scope = $this->scope;
        }
        return $this->template->clearAssign($spec, $scope);
    }

    /**
     * Returns a specified value or all values.
     *
     * @param   string|null $spec
     * @return  mixed|array
     */
    public function getAssign($spec = null)
    {
        return $this->template->getTemplateVars($spec);
    }

    /**
     * Renders the current template.
     *
     * @return  string
     */
    public function render()
    {
        return $this->template->fetch();
    }

    /**
     * Fetch an template by name.
     *
     * @param   $template_name
     * @return  string
     */
    public function fetch($template_name)
    {
        return $this->engine->fetch($template_name, $this->template);
    }

    /**
     * @param   bool $value
     * @return  Enlight_View_Default
     */
    public function setNocache($value = true)
    {
        $this->nocache = (bool)$value;
        return $this;
    }

    /**
     * @param   int|null $scope
     * @return  Enlight_View_Default
     */
    public function setScope($scope = null)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @param   bool $value
     * @return  Enlight_View_Default
     */
    public function setCaching($value = true)
    {
        $this->template->caching = (bool)$value;
        return $this;
    }

    /**
     * @return  bool
     */
    public function isCached()
    {
        return $this->template !== null ? $this->template->isCached() : false;
    }

    /**
     * @param   string|array $cache_id
     * @return  Enlight_View_Default
     */
    public function setCacheId($cache_id = null)
    {
        $this->template->setCacheId($cache_id);
        return $this;
    }

    /**
     * @param   string|array $cache_id
     * @return  Enlight_View_Default
     */
    public function addCacheId($cache_id)
    {
        $this->template->addCacheId($cache_id);
        return $this;
    }

    /**
     * @return  string
     */
    public function getCacheId()
    {
        return $this->template->cache_id;
    }
}