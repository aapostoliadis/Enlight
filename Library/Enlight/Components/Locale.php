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
 * @package    Enlight_Locale
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Locale
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Components_Locale extends Zend_Locale
{
    /**
     * Unique id for the locale class.
     * @var int
     */
    protected $id;

    /**
     * Returns currency id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets a new locale
     *
     * @param  string|array|Zend_Locale $locale (Optional) New locale to set
     * @return Enlight_Components_Locale
     */
    public function setLocale($locale = null)
    {
        if (is_array($locale)) {
            $this->id = isset($locale['id']) ? (int)$locale['id'] : null;
            $locale = isset($locale['locale']) ? $locale['locale'] : null;
        }
        parent::setLocale($locale);
        return $this;
    }
}