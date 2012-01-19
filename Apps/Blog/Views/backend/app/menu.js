/**
 * Shopware Menu
 *
 * This component creates the main backend menu. The data for the items
 * array are placed in a global variable named "backendMenu".
 *
 * Note that this component are based on the Ext.toolbar.Toolbar instead
 * of Ext.menu.Menu.
 *
 * @author: s.pohl <stp@shopware.de>
 * @date: 2011-11-30
 */
Ext.define('Shopware.app.Menu', {
	extend: 'Ext.toolbar.Toolbar',
	alias: 'widget.mainmenu',
	alternateClassName: 'Shopware.Menu',

	ui: 'shopware-ui',
	dock: 'top',
	height: 40,

	/**
	 * Creates the menu and sets the component items
	 */
	initComponent: function() {

		this.items = backendMenu;

		this.callParent(this);

		if(Shopware.app.Application.getEnvironment() == 'development') {
			this.debugButtons();
		}

		this.items.add(Ext.create('Shopware.app.Search'));
	},

	/**
	 * Creates buttons for debugging and testing
	 * @deprecated
	 */
	debugButtons: function() {
		this.add(Ext.create('Ext.button.Button', {
			text: 'Benutzerverwaltung (Neu)',
			iconCls: 'user',
			handler: function() { openNewModule('Shopware.apps.Auth') }
		}));
		
		this.add(Ext.create('Ext.button.Button', {
            text: 'Hersteller (Neu)',
            iconCls: 'lorry',
            handler: function() { openNewModule('Shopware.apps.Suppliers') }
        }));

		this.add(Ext.create('Ext.button.Button', {
			text: 'HUD Demo',
			iconCls: 'window',
			handler: function() { openHudDemo() }
		}));

		this.add(Ext.create('Ext.button.Button', {
			text: '[DEBUG]Count DOM elements',
			handler: function() { alert('Used DOM elements: ' + document.getElementsByTagName('*').length); }
		}));

		this.add(Ext.create('Ext.button.Button', {
			text: '[DEBUG]Create Growl Message',
			iconCls: 'growl',
			handler: function() {
				Ext.Msg.prompt('Create Growl Example', 'Please enter your Growl text:', function(btn, text){
    				if (btn == 'ok'){
       					Shopware.app.Growl.open('Growl Example', text);
   					 }
				});
			}
		}));
	}
})