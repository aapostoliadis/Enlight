Ext.define('Enlight.apps.BlogPosts.view.main.Create', {
    extend:'Ext.window.Window',
    alias:'widget.postcreate',
    title:'Create Post',
    layout:'fit',
    autoShow:true,
    border:0,
    items: [],

    initComponent:function () {
        this.record = this.record || null;

        this.createPostForm = Ext.create('Ext.form.Panel', {
            defaults:{
                labelStyle:'font-weight: 700; text-align: right;',
                labelWidth:100
            },
            bodyPadding:10,
            items:[
                {
                    xtype:'hiddenfield',
                    name: 'id'
                },
                {
                    xtype:'textfield',
                    name:'headline',
                    fieldLabel:'Headline'
                },
                {
                    xtype: 'datefield',
                    name: 'creation_date',
                    format:'Y-m-d',
                    fieldLabel: 'Creation Date',
                    value: new Date()
                },
                {
                    xtype:'htmleditor',
                    name:'content',
                    height: 250,
                    fieldLabel:'Content'
                }
            ]
        });

//        // Load our record if necessary
//        if(this.record) {
//            // Add the form panel to our items array
//            this.createPostForm.getForm().loadRecord(this.record);
//        }
//        // Add the form panel to our items array
        this.items = [this.createPostForm];

        // Create our action  buttons
        this.buttons = [
            {
                text:'cancel',
                scope:this,
                handler:this.close
            },
            {
                text:'Save',
                action:'save',
                scale:'large'
            }
        ];

        this.callParent(arguments);
    }
});