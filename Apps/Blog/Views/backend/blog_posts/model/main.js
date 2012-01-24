Ext.define('Enlight.apps.BlogPosts.model.Main', {
	extend: 'Ext.data.Model',
	fields: ['id', 'headline', 'content', 'creation_date'],
	proxy: {
		type: 'ajax',
		api: {
			read: '{url module=backend controller=blog_posts action=getPosts}',
			create: '{url module=backend controller=blog_posts action=createPost}',
			update: '{url module=backend controller=blog_posts action=updatePost}',
			destroy: '{url module=backend controller=blog_posts action=deletePost}'
		},
		reader: {
			type: 'json',
			root: 'data'
		}
	}
});