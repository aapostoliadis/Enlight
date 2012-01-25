Ext.define('Enlight.apps.BlogPosts', {
	extend: 'Enlight.app.SubApplication',
    loadPath: "{url module=backend controller=BlogPosts action=load}?file=",
	controllers: [ 'Main' ]
});