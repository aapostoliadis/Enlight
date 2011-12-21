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
 * @package    Enlight_Site
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Site
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Components_Site
{
    /**
     * @var array
     */
    protected $properties = array();

    /**
     * @var array
     */
    protected $resources = array();

    /**
     * Class constructor, initializes the basic options
     *
     * @param null|array|Enlight_Config $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof Enlight_Config) {
            $options = $options->toArray();
        }
        if ($options !== null) {
            $this->setOptions($options);
        }
    }

    /**
     * Sets the options
     *
     * @param   array $options
     * @return  Enlight_Components_Site
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $option) {
            $key = strtolower($key);
            switch ($key) {
                case 'id':
                    $this->properties[$key] = (int) $option;
                    break;
                case 'name':
                    $this->properties[$key] = (string) $option;
                    break;
                case 'locale':
                    $this->setLocale($option);
                    break;
                case 'currency':
                    $this->setCurrency($option);
                    break;
                case 'locale' . 'switch':
                case 'currency' . 'switch':
                case 'site' . 'switch':
                    if (!is_array($option)) {
                        $option = explode('|', $option);
                    }
                    $this->properties[$key] = $option;
                    break;
                case 'host':
                    $this->setHost($option);
                    break;
                case 'template':
                    $this->setTemplate($option);
                    break;
                default:
                    $this->properties[$key] = $option;
                    break;
            }
        }
        return $this;
    }

    /**
     * Sets a property by name.
     *
     * @param   $property
     * @param   $value
     * @return  Enlight_Components_Site
     */
    public function set($property, $value)
    {
        $method = 'set' . self::normalizePropertyName($property);

        if ($method != 'setOptions' && method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $property = strtolower($property);
            $this->properties[$property] = $value;
        }

        return $this;
    }

    /**
     * Returns a property by name.
     *
     * @param   $property
     * @return  mixes
     */
    public function get($property)
    {
        $property = strtolower($property);
        if(isset($this->properties[$property])) {
            return $this->properties[$property];
        }
        return null;
    }

    /**
     * Set site host method
     *
     * @param   int|string $host
     * @return  Enlight_Components_Site
     */
    public function setHost($host = null)
    {
        if ($host === null && isset($this->properties['host'])) {
            $host = $this->properties['host'];
        }
        if (empty($host) && isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        }
        $this->properties['host'] = trim($host);
        return $this;
    }

    /**
     * Sets / loads the locale instance.
     *
     * @param   int|string|Zend_Locale $locale
     * @return  Enlight_Components_Site
     */
    public function setLocale($locale = null)
    {
        if ($locale === null && isset($this->properties['locale'])) {
            $locale = $this->properties['locale'];
        } elseif ($locale !== null ) {
            $this->properties['locale'] = $locale;
        }
        $this->resources['locale'] = new Enlight_Components_Locale($locale);
        unset($this->properties['currency']);
        return $this;
    }

    /**
     * Sets / loads the currency instance.
     *
     * @param   null $currency
     * @return  Enlight_Components_Site
     */
    public function setCurrency($currency = null)
    {
        if ($currency === null && isset($this->properties['currency'])) {
            $currency = $this->properties['currency'];
        } elseif ($currency !== null ) {
            $this->properties['currency'] = $currency;
        }
        $this->resources['currency'] = new Enlight_Components_Currency($currency, $this->Locale());
        return $this;
    }

    /**
     * @param   null|string|array $template
     */
    public function setTemplate($template = null)
    {
        if ($template === null && isset($this->properties['template'])) {
            $template = $this->properties['template'];
        } elseif ($template !== null ) {
            $this->properties['template'] = $template;
        }

        if (is_string($template)) {
            $template = array('template_dir' => $template);
        }
        $template = (array) $template;
        if (!isset($template['compile_id'])) {
            $template['compile_id'] = $this->getName() . '|' .  $this->Locale()->toString();
        }

        $this->resources['template'] = new Enlight_Template_Manager($template);
    }

    /**
     * @return  int
     */
    public function getId()
    {
        return isset($this->properties['id']) ? $this->properties['id'] : null;
    }

    /**
     * @return  string
     */
    public function getName()
    {
        if (isset($this->properties['name'])) {
            return $this->properties['name'];
        } else {
            return 'site' . $this->getId();
        }
    }

    /**
     * Returns shop locale
     *
     * @return  string
     */
    public function getHost()
    {
        if (!isset($this->properties['host'])) {
            $this->setHost();
        }
        return $this->properties['host'];
    }

    /**
     * Returns shop locale
     *
     * @return  Zend_Locale
     */
    public function Locale()
    {
        if (!isset($this->resources['locale'])) {
            $this->setLocale();
        }
        return $this->resources['locale'];
    }

    /**
     * Returns shop currency
     *
     * @return  Zend_Currency
     */
    public function Currency()
    {
        if (!isset($this->resources['currency'])) {
            $this->setCurrency();
        }
        return $this->resources['currency'];
    }

    /**
     * @return Enlight_Template_Manager
     */
    public function Template()
    {
        if (!isset($this->resources['template'])) {
            $this->setTemplate();
        }
        return $this->resources['template'];
    }

    /**
     * @return array
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Sleep instance method
     *
     * @return array
     */
    public function __sleep()
    {
        return array('properties');
    }

    /**
     * Wakeup instance method
     */
    public function __wakeup()
    {
    }

    /**
     * Normalizes a property name
     *
     * @param  string $property  property name to normalize
     * @return string            normalized property name
     */
    protected static function normalizePropertyName($property)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));
    }
}