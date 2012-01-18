Ext.define('Enlight.apps.Auth.store.Main', {
	extend: 'Ext.data.Store',
	autoLoad: true,
	groupField: 'admin',
	pageSize: 30,
	model : 'Enlight.apps.Auth.model.Main'
});