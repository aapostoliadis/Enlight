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
 * @package    Enlight_ExtJs
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Stephan P.
 * @author     $Author$
 */

/**
 * Overrides the Ext.button.Button to provide
 * an additional HTML5 data attribute to provide
 * a better adressing in selenium ui tests.
 *
 * @category   Enlight
 * @package    Enlight_ExtJs
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
Ext.override(Ext.form.Field, {

   ui: 'default shopware-ui',

   supportText: '',
   helpText: '',
   helpWidth: null,
   helpTitle: null,
   style: 'margin: 0 0 1em',

   /** Triggers the rendering of the support and help texts */
   afterRender: function() {
       this.callParent();
       if(this.helpText) {
           this.createHelp();
       }

	   if(this.supportText) {
	      this.createSupport();
	   }
   },

   /* Creates the support text which will be rendered under the actual form element */
   createSupport: function() {
       var supportText = new Ext.Element(document.createElement('div'));
       supportText.set({
           cls: 'x-form-support-text',
           style: 'font-style: italic; color: #999; margin: 24px 0 0;'
       });

       if(this.xtype === 'checkbox' || this.$className == 'Ext.form.field.Checkbox') {
       		supportText.set({ style: 'font-style: italic; color: #999; margin: 6px 0 0;' })
       }

       if(this.supportText) {
           supportText.update(this.supportText);
       }
       supportText.appendTo(this.getEl().select('.x-form-item-body'));

       return supportText;
   },

   /** Create the help icon and it's responsible quick tip */
   createHelp: function() {
       var helpIcon = new Ext.Element(document.createElement('img'));
       helpIcon.set({
           cls: 'x-form-help-icon',
           src: 'help.png',
           style: 'position: absolute; right: -24px; top: 3px;',
           width: 16,
           height: 16
       });
       helpIcon.appendTo(this.getEl());

       Ext.tip.QuickTipManager.register({
           target: helpIcon,
           title: (this.helpTitle) ? this.helpTitle : 'Hilfe',
           text: this.helpText,
           width: (this.helpWidth) ? this.helpWidth : 175,
           dismissDelay: 10000
       });

       return helpIcon;
   }

});