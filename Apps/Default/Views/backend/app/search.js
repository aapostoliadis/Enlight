 Ext.define('Shopware.app.Search', {
	extend: 'Ext.container.Container',
	alias: 'widget.searchfield',

	cls: 'searchfield',

	// Min length of the search query
	minSearchLength: 3,

	// Request url
	requestUrl: basePath + '/backend/search',

	/**
	 * Initialize the search and creates the search field and
	 * the drop down menu
	 */
	initComponent: function() {
		var me = this;

		me.callParent(me);

		me.searchField = Ext.create('Ext.form.field.Text', {
			emptyText: 'Suche...',
			cls: 'searchfield',
			margin: '5 0',
			allowBlank: true,
			enableKeyEvents: true,
			listeners: {
				scope: me,
				keyup: me.onKeyUp
			}

		});

		me.searchDropDown = Ext.create('Ext.container.Container', {
			cls: 'search-dropdown',
			renderTo: Ext.getBody(),
			style: 'position: fixed; z-index: 20030',
			hidden: true
		});

		me.add(me.searchField);
	},

	/**
	 * Event listener which sends the AJAX request and replaces
	 * the content of the drop down menu
	 *
	 * @param field
	 */
	onKeyUp: function(field) {
		var value = field.getValue(),
			me = this;

		// Check the length of the search query
		if(value.length < this.minSearchLength) {
			me.searchDropDown.update('');
			me.searchDropDown.hide();
			return false;
		}

		var result = ''

		// Request the search result
		Ext.Ajax.request({
			url: me.requestUrl,
			params: { search: value },
			method: 'POST',
			success: function(response) {
				var html = response.responseText,
					position = me.searchField.getPosition();

				me.searchDropDown.update(html);
				me.searchDropDown.getEl().applyStyles({
					left: position[0]+'px',
					top: position[1]+21+'px'
				});
				me.searchDropDown.show();
			}
		})

	}
})