<script type="text/javascript">
//<![CDATA[
Ext.ns('Shopware.Auth.Components');
Shopware.Auth.Components.LoginPanel = Ext.extend(Ext.form.FormPanel, {
    title: '{s name="PanelTitle"}Login Shopware Backend{/s}',
    width: 440,
    height: 180,
    padding: 3,
    layout: 'auto',
    x: 500,
    y: 240,
    frame: true,
    initComponent: function() {
    	this.emblem = new Ext.form.FieldSet({
        	id: 'emblem',
            width: 120,
            height: 140,
            title: '',
            border: false,
            style: { background:"url({link file='backend/_resources/images/index/logo_login.png'}) no-repeat center center"}
        });

        this.submit = function() {
        	this.getForm().submit({ url: '{url action="login"}', waitMsg:'{s name="WaitMessage"}Login...{/s}'});
        };

        this.keyListener = { 'specialkey': function(el, e){
        	if (e.getKey() == e.ENTER) {
        		this.submit();
        	}
        }, scope:this };

        this.items = [{
            xtype: 'fieldset',
            width: 300,
            title: '',
            border: false,
            style: 'float:left',
            items: [{
                xtype: 'textfield',
                name:'username',
                fieldLabel: '{s name="UserNameField"}Benutzername{/s}',
                anchor: '100%',
                allowBlank: false,
                blankText: '{s name="UserNameMessage"}Geben Sie einen Benutzernamen an!{/s}',
                listeners: this.keyListener
            }, {
                xtype: 'textfield',
                name:'password',
                fieldLabel: '{s name="PasswordField"}Passwort{/s}',
                inputType: 'password',
                anchor: '100%',
                allowBlank: false,
                blankText: '{s name="PasswordMessage"}Geben Sie ein Passwort an!{/s}',
                listeners: this.keyListener
            }, {
                xtype: 'combo',
                name:'locale',
                hiddenName:'locale',
                fieldLabel: '{s name="LocaleField"}Sprache{/s}',
                mode: 'local',
                anchor: '100%',
                valueField: 'id',
                displayField: 'value',
                editable:false,
		      	forceSelection : true,
		      	triggerAction:'all',
		      	{if $LocaleOptions}
		      	value: {$Locale->getId()},
                store: new Ext.data.SimpleStore({
                	fields: ['id', 'value'],
                	data: [ {foreach $LocaleOptions as $LocaleOption}
                		[
                			{$LocaleOption->getId()},
                			{$LocaleOption->getLanguage()|translate:language|escape:json}+' ('+{$LocaleOption->getRegion()|translate:territory|escape:json}+')'
                		]{if !$LocaleOption@last},{/if}
                	{/foreach} ]
                })
                {else}
                value: 1,
                store: new Ext.data.SimpleStore({
                	fields: ['id', 'value'],
                	data: [[1, 'Deutsch']]
                }),
                disabled: true
                {/if}
            }, {
                xtype: 'button',
                text: '{s name="LoginButton"}Anmelden{/s}',
                iconCls: 'icon-key',
                style:'margin-top:10px;width:80px;float:right;',
                handler: function() {
                	this.submit();
                },
                scope: this
            }]
        }, this.emblem ];

        Shopware.Auth.Components.LoginPanel.superclass.initComponent.call(this);
    },
    actionComplete: function(el, action){
    	if(action.result.success == true){
	    	document.location.href = "{url controller='index'}";
		}
    },
    actionFailed: function(el,action){
		if (action.result.lockeduntil != ""){
			Ext.Msg.show({
			   title: '{s name="ErrorTitleLockedUntil"}Zu viele fehlgeschlagene Versuche!{/s}',
			   msg: '{s name="ErrorMessageLockedUntil"}Ihr Administrationskonto ist bis '+action.result.lockeduntil+' gesperrt!{/s}',
			   buttons: Ext.Msg.OK,
			   icon: Ext.MessageBox.ERROR
			});
		}else {
			Ext.Msg.show({
			   title: '{s name="ErrorTitle"}Anmeldung fehlgeschlagen!{/s}',
			   msg: '{s name="ErrorMessage"}Ihre Zugangsdaten konnten keinem Benutzer zugeordnet werden.{/s}',
			   buttons: Ext.Msg.OK,
			   icon: Ext.MessageBox.ERROR
			});
		}
    },
    initEvents: function(){
		this.on('afterrender', this.setPanelPosition, this);
		this.parent.on('resize', this.setPanelPosition, this);
		this.on('actioncomplete', this.actionComplete, this);
		this.on('actionfailed', this.actionFailed, this);
		Shopware.Auth.Components.LoginPanel.superclass.initEvents.call(this);
    },
    setPanelPosition: function(){
    	vheight = this.parent.getHeight();
    	pheight = this.getHeight();
    	theight = vheight-pheight;

    	vwidth = this.parent.getWidth();
    	pwidth = this.getWidth();
    	twidth = vwidth-pwidth;

    	this.setPosition(twidth/2, theight/2);
    }
});
//]]>
</script>v