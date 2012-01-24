Ext.define('Enlight.apps.BlogPosts', {
	extend: 'Enlight.app.SubApplication',
    loadPath: "{url module=backend controller=blog_posts action=load}?file=",
	controllers: [ 'Main' ]
});