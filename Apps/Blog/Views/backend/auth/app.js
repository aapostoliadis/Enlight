Ext.define('Enlight.apps.Auth', {
	extend: 'Enlight.app.SubApplication',
    loadPath: "{url module=backend controller=auth action=load}?file=",
	controllers: [ 'Main' ]
});