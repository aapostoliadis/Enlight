Ext.define('Enlight.apps.BlogPosts.store.Main', {
	extend: 'Ext.data.Store',
	autoLoad: true,
	pageSize: 30,
	model : 'Enlight.apps.BlogPosts.model.Main'
});