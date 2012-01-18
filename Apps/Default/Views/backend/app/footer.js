/**
 * Shopware UI - Footer
 *
 * Special Ext.toolbar.Toolbar, which is docked
 * to the bottom and contains a special context
 * menu.
 *
 * Note that the component will be streched to
 * the full viewport width.
 *
 * @author: s.pohl <stp@shopware.de>
 * @date: 2011-12-05
 */
Ext.define('Shopware.app.Footer', {
	extend: 'Ext.toolbar.Toolbar',
	alias: 'wiget.footer',
	alternateClassName: 'Shopware.Footer',

	height: 30,
	ui: 'shopware-ui',
	dock: 'bottom',
	layout: { overflowHandler: 'Scroller' },

	/** Holds our window context menu */
	contextMenu: null,

	initComponent: function() {
		var me = this;

		this.createCommonItems();

		me.contextMenu = Ext.create('Ext.menu.Menu', me.createContextMenu());

		me.callParent(me);
	},

	/**
	 * Creates the default buttons for the footer taskbar
	 */
	createCommonItems: function() {
		var me = this;

		me.logoutBtn = Ext.create('Ext.button.Button', {
			text: 'Logout',
			handler: me.onLogoutBtn
		});

		me.shopViewBtn = Ext.create('Ext.button.Button', {
			text: 'Shop-Ansicht',
			handler: me.onShopViewBtn
		});

        me.dashboardBtn = Ext.create('Ext.button.Button', {
            text: 'Dashboard',
            handler: me.onDashboardBtn
        });

        me.adminBtn = Ext.create('Ext.button.Button', {
            text: 'Verwaltung',
            handler: me.onAdminstrationBtn
        });

		this.items = [
            me.logoutBtn,
            me.shopViewBtn,
            { xtype: 'tbseparator' },
            me.dashboardBtn,
            me.adminBtn,
            { xtype: 'tbseparator' }
        ]
	},

	/**
	 * Retunrs the configuration object for the context menu
	 *
	 * @return object
	 */
	createContextMenu: function() {
		var me = this;

		return {
			defaultAlign: 'br-tr',
			items: [
				{ text: 'Send to back', scope: me, handler: me.onSendToBack },
				{ text: 'Bring to front', scope: me, handler: me.onBringToFront },
				{ text: 'Minimize', scope: me, handler: me.onMinimize },
				{ text: 'Maximize', scope: me, handler: me.onMaximize },
				'-',
				{ text: 'Close', scope: me, handler: me.onClose }
			]
		};
	},

	/**
	 * Getter method which returns the footer context menu
	 * @return
	 */
	getContextMenu: function() {
		return this.contextMenu;
	},

	/**
	 * Event handler which closes the active Ext.window.Window
	 *
	 * @param btn - the pressed context menu Ext.button.Button
	 */
	onClose: function(btn) {
		var win = this.getActiveWindow(btn);

		win.close();
	},

	/**
	 * Event handler which maximizes the active Ext.window.Window
	 *
	 * @param btn - the pressed context menu Ext.button.Button
	 */
	onMaximize: function(btn) {
		var win = this.getActiveWindow(btn);

		win.minimized = false;
		win.active = true;
		win.maximize();
	},

	/**
	 * Event handler which minimizes the active Ext.window.Window
	 *
	 * @param btn - the pressed context menu Ext.button.Button
	 */
	onMinimize: function(btn) {
		var win = this.getActiveWindow(btn);

		win.minimized = true;
		win.active = false;
		win.minimize();
	},

	/**
	 * Event handler which brings the active Ext.window.Window to
	 * the front
	 *
	 * @param btn - the pressed context menu Ext.button.Button
	 */
	onBringToFront: function(btn) {
		var win = this.getActiveWindow(btn);

		win.active = true;
		Ext.WindowManager.bringToFront(win);
	},

	/**
	 * Event handler which sends the active Ext.window.Window to
	 * the back
	 *
	 * @param btn - the pressed context menu Ext.button.Button
	 */
	onSendToBack: function(btn) {
		var win = this.getActiveWindow(btn);

		win.active = false;
		Ext.WindowManager.sendToBack(win);
	},

	/**
	 * Helper method which returns the active Ext.window.Window.
	 * The window is assigned to the passsed Ext.button.Button
	 *
	 * @param btn - the pressed context menu Ext.button.Button
	 * @return the active Ext.window.Window
	 */
	getActiveWindow: function(btn) {
		return btn.parentMenu.activeWindow;
	},

	/**
	 * Event handler which logs out the user
	 */
	onLogoutBtn: function() {
		window.location.href = basePath + '/backend/index/logout';
	},

	/**
	 * Event handler which loads the live preview of the shop
	 */
	onShopViewBtn: function() {
		window.loadSkeleton('live', true);
	},

    onDashboardBtn: function() {
        Shopware.app.VerticalScroll.scroll('left');
    },

    onAdminstrationBtn: function() {
        Shopware.app.VerticalScroll.scroll('right');
    }
})