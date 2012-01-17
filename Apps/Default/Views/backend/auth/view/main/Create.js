Ext.define('Enlight.apps.Auth.view.main.Create', {
    extend: 'Ext.window.Window',
    alias : 'widget.usercreate',

    title : 'Benutzer anlegen',
    layout: 'fit',
    autoShow: true,
	border: 0,

    initComponent: function() {
        this.items = [
            {
                xtype: 'form',
				defaults:{
					labelStyle: 'font-weight: 700; text-align: right;',
					labelWidth: 100
				},
				bodyPadding: 10,
                items: [{
					xtype: 'textfield',
					name : 'username',
					fieldLabel: 'Benutzername'
				}, {
					xtype: 'textfield',
					name: 'password',
					inputType: 'password',
					fieldLabel: 'Passwort'
				}, {
					xtype: 'textfield',
					name: 'name',
					fieldLabel: 'Name'
				}, {
					xtype: 'textfield',
					name : 'email',
					fieldLabel: 'eMail'
				}, {
					xtype: 'checkbox',
					name: 'admin',
					boxLabel: 'Benutzer hat Zugriff auf alle Module'
				}]
            }
        ];

        this.buttons = [{
			text: 'Abbrechen',
			scope: this,
			handler: this.close
        }, {
			text: 'Speichern',
			action: 'save',
			scale: 'large'
        }];

        this.callParent(arguments);
    }
});