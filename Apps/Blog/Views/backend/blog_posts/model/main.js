Ext.define('Enlight.apps.BlogPosts.model.Main', {
	extend: 'Ext.data.Model',
	fields: ['id', 'headline', 'content', 'creation_date'],
	proxy: {
		type: 'ajax',
		api: {
			read: 'backend/BlogPosts/getPosts',
			create: 'backend/BlogPosts/createPost',
			update: 'backend/BlogPosts/updatePost',
			destroy: 'backend/BlogPosts/deletePost'
		},
		reader: {
			type: 'json',
			root: 'data'
		}
	}
});