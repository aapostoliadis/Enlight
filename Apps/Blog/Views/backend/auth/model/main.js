Ext.define('Enlight.apps.Auth.model.Main', {
	extend: 'Ext.data.Model',
	fields: ['id', 'username', 'password', 'lastlogin', 'name', 'email', 'active', 'admin' ],
	proxy: {
		type: 'ajax',
		api: {
			read: 'backend/auth/getUsers',
			create: 'backend/auth/createUser',
			update: 'backend/auth/updateUser',
			destroy: 'backend/auth/deleteUser'
		},
		reader: {
			type: 'json',
			root: 'data'
		}
	},
	validations: [
		{ field: 'username', type: 'length', min: 6 },
		{ field: 'password', type: 'length', min: 6 },
		{ field: 'name', type: 'length', min: 6 }
	]
});