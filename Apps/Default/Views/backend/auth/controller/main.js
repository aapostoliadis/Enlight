Ext.define('Enlight.apps.Auth.controller.Main', {
	extend: 'Ext.app.Controller',
	views: [ 'main.List', 'main.Create' ],
	stores: [ 'Main' ],
	models: [ 'Main' ],

	refs: [
		{ ref: 'grid', selector: 'grid' },
		{ ref: 'addBtn', selector: 'button[action=add]' },
		{ ref: 'saveBtn', selector: 'window button[action=save]' }
	],

	/**
	 * Creates the neccessary event listener for this
	 * specific controller and opens a new Ext.window.Window
	 * to display the subapplication
	 */
	init: function() {

		this.control({
			'grid button[action=add]': {
				click: this.onCreateUser
			},
			'window button[action=save]': {
				click: this.onSaveUser
			},
			'grid button[action=deleteUsers]': {
				click: this.deleteMultipleUsers
			},
			'grid button[action=deleteUser]': {
				click: this.deleteSingleUser
			}
		});

		Ext.create('Ext.window.Window', {
    		layout: 'border',
    		width: 800,
    		height: 600,
			maximizable: true,
    		border: 0,
    		title: 'Benutzerverwaltung',
    		items: [ this.getView('main.List') ]
    	}).show();
	},

	/**
	 * Opens the Ext.window.window which displays
	 * the Ext.form.Panel to create a new user
	 */
	onCreateUser: function() {
		Ext.widget('usercreate');
	},

	/**
	 * Event listener method which adds a user
	 * @param button
	 */
	onSaveUser: function(button) {

		var win = button.up('window'),
			form = win.down('form'),
			values = form.getValues(),
			user = Ext.create('Enlight.apps.Auth.model.Main', values),
			store = this.getStore('Main'),
			grid = this.getGrid();

		grid.setLoading(true);
		user.save({
			success: function() {
				grid.setLoading(false);
				store.add(user);
				win.close();
			}
		});
	},

	/**
	 * Event listener method which deletes multiple users
	 */
	deleteMultipleUsers: function() {

		var grid = this.getGrid(),
			sm = grid.getSelectionModel(),
			selection = sm.selected.items,
			store = this.getStore('Main');

		Ext.MessageBox.confirm('Ausgew&auml;hlte Benutzer l&ouml;schen', 'Sind Sie sicher dass Sie die markierten Benutzer l&ouml;schen m&ouml;chten?', function(response) {
			if(response == 'yes') {
				grid.setLoading(true);
				Ext.each(selection, function(user) {
					var userModel = Ext.create('Enlight.apps.Auth.model.Main', user.data);
					userModel.destroy({
						success: function() {
							store.remove(userModel);
						}
					});
				});

				store.load();
				grid.setLoading(false);
			} else {
				return false;
			}
		})
	}
});