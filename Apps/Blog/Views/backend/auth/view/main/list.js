Ext.define('Enlight.apps.Auth.view.main.List', {
	extend: 'Ext.grid.Panel',
	alias: 'widget.userlist',
	region: 'center',
	autoScroll: true,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		listeners: {
			selectionchange: function(sm, selections) {
				var owner = this.view.ownerCt,
					btn = owner.down('button[action=deleteUsers]')

				btn.setDisabled(selections.length == 0);
			}
		}
	}),

	store: 'Main',

	// Add paging toolbar to the bottom toolbar
	dockedItems: [{
		dock: 'bottom',
		xtype: 'pagingtoolbar',
		displayInfo: true,
		store: 'Main'
	}],

	// Event listeners
	listeners: {
		scope: this,

		// Sample event listener which will be fired when the user has edited a grid row
		edit: function(editor) {
			editor.grid.setLoading(true);
			window.setTimeout(function() {
				editor.store.sync();
				editor.grid.setLoading(false);
			}, 500);
		}
	},

	initComponent: function() {

		// Define the columns and renderers
		this.columns = [
		{
			header: 'Benutzername',
			dataIndex: 'username',
			flex: 1,
			renderer: this.nameColumn
		}, {
			header: 'Name',
			dataIndex: 'name',
			flex: 1
		}, {
			header: 'E-Mail Adresse',
			dataIndex: 'email',
			flex: 1,
			renderer: this.emailColumn
		}, {
			header: 'Gruppe',
			dataIndex: 'admin',
			flex: 1,
			renderer: this.adminColumn
		}, {
			xtype: 'actioncolumn',
			width: 50,
			items: [{
				iconCls: 'delete',
				tooltip: 'Löscht diesen Benutzer',
				handler: function(grid, rowIndex) {
					var store = grid.getStore(),
						record = store.getAt(rowIndex);

					Ext.MessageBox.confirm('Ausgew&auml;hlten Benutzer l&ouml;schen', 'Sind Sie sicher dass Sie die ausgew&auml;hlten Benutzer l&ouml;schen m&ouml;chten?', function(response) {
						if(response !== 'yes') {
							return false;
						}
						grid.setLoading(true);

						var userModel = Ext.create('Enlight.apps.Auth.model.Main', record.data);
						userModel.destroy({
							success: function() {
								store.remove(userModel);
								store.load();
								grid.setLoading(false);
							}
						});

					});
				}
			}, {
				iconCls: 'pencil',
				tooltip: 'Bearbeiten Sie diesen Benutzer'
			}]
		}];

		// Row grouping
		this.groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
		    groupHeaderTpl: 'Gruppe: <tpl if="name == 1">Administrator</tpl><tpl if="name == 0">Benutzer</tpl> ({literal}{rows.length}{/literal})'
		});
		this.features = [ this.groupingFeature ];

		// Toolbar
		this.toolbar = Ext.create('Ext.toolbar.Toolbar', {
			dock: 'top',
		    items: [{
				iconCls: 'add',
				text: 'Hinzufügen',
				action: 'add'
			}, {
				iconCls: 'delete',
				text: 'Markierte Einträge löschen',
				disabled: true,
				action: 'deleteUsers'
			},
			'->',
			{
				xtype: 'textfield',
				name: 'searchfield',
				width: 175,
				enableKeyEvents: true,
				emptyText: 'Suchbegriff',
				listeners: {
					keyup: this.onSearchField
				}
			}]
		});

		this.dockedItems = Ext.clone(this.dockedItems);
		this.dockedItems.push(this.toolbar);

		this.callParent();
	},

	/**
	 * Formats the name column
	 * @param value
	 */
	nameColumn: function(value) {
		return Ext.String.format('<strong style="font-weight: 700">{0}</strong>', value);
	},

	/**
	 * Formats the email column
	 * @param value
	 */
	emailColumn: function(value) {
		return '<a href="mailto:' + value + '">' + value + '</a>';
	},

	/**
	 * Formats the admin (group) column
	 * @param value
	 */
	adminColumn: function(value) {
		return (~~value === 1) ? 'Administrator' : 'Benutzer';
	},

	/**
	 * Performs a filtering in the grid
	 * @param field
	 */
	onSearchField: function(field) {
		var grid = this.ownerCt.ownerCt.view,
			store = grid.store;

		if(this.value.length == 0) {
			store.clearFilter();
			return false;
		}

		store.filter('username', this.value);
		return true;
	},

	deleteSingleUser: function() {
	}
});