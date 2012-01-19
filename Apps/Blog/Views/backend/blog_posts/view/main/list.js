Ext.define('Enlight.apps.BlogPosts.view.main.List', {
    extend:'Ext.grid.Panel',
    alias:'widget.userlist',

    /**
     * Describes the rendering region
     * @cfg { string } region
     */
    region:'center',
    autoScroll:true,
    selModel:Ext.create('Ext.selection.CheckboxModel', {
        listeners:{
            selectionchange:function (sm, selections) {
                var owner = this.view.ownerCt,
                        btn = owner.down('button[action=deletePosts]');

                btn.setDisabled(selections.length == 0);
            }
        }
    }),

    store:'Main',

    // Add paging toolbar to the bottom toolbar
    dockedItems:[
        {
            dock:'bottom',
            xtype:'pagingtoolbar',
            displayInfo:true,
            store:'Main'
        }
    ],

    // Event listeners
    listeners:{
        scope:this,

        // Sample event listener which will be fired when the user has edited a grid row
        edit:function (editor) {
            editor.grid.setLoading(true);
            window.setTimeout(function () {
                editor.store.sync();
                editor.grid.setLoading(false);
            }, 500);
        }
    },

    initComponent:function () {

        // Define the columns and renderers
        this.columns = [
            {
                header:'Headline',
                dataIndex:'headline',
                flex:1,
                renderer:this.headlineColumn
            },
            {
                header:'Content',
                dataIndex:'content',
                flex:1
            },
            {
                header:'Creation Date',
                xtype:'datecolumn',
                format:'Y-m-d',
                dataIndex:'creation_date',
                flex:1
            },
            {
                xtype:'actioncolumn',
                width:50,
                items:[
                    {
                        iconCls:'delete',
                        cls:'delete',
                        tooltip:'Deletes this post',
                        handler:function (grid, rowIndex) {
                            var store = grid.getStore(),
                                record = store.getAt(rowIndex);

                            Ext.MessageBox.confirm('Delete chosen post?', 'Do you really want to delete the chosen post?', function (response) {
                                if (response !== 'yes') {
                                    return false;
                                }
                                grid.setLoading(true);

                                var blogPostsModel = Ext.create('Enlight.apps.BlogPosts.model.Main', record.data);
                                blogPostsModel.destroy({
                                    success:function () {
                                        store.remove(blogPostsModel);
                                        store.load();
                                        grid.setLoading(false);
                                    }
                                });

                            });
                        }
                    },
                    {
                        iconCls:'pencil',
                        cls:'editBtn',
                        tooltip:'Edit this item'
                    }
                ]
            }
        ];

        // Toolbar
        this.toolbar = Ext.create('Ext.toolbar.Toolbar', {
            dock:'top',
            items:[
                {
                    iconCls:'add',
                    text:'Add',
                    action:'add'
                },
                {
                    iconCls:'delete',
                    text:'Delete marked items',
                    disabled:true,
                    action:'deletePosts'
                },
                '->',
                {
                    xtype:'textfield',
                    name:'searchfield',
                    width:175,
                    enableKeyEvents:true,
                    emptyText:'Searchterm',
                    listeners:{
                        keyup:this.onSearchField
                    }
                }
            ]
        });

        this.dockedItems = Ext.clone(this.dockedItems);
        this.dockedItems.push(this.toolbar);

        this.callParent();
    },

    /**
     * Formats the name column
     * @param value
     */
    headlineColumn:function (value) {
        return '<strong style="font-weight: 700">' + value + '</strong>';
    },

    /**
     * Performs a filtering in the grid
     * @param field
     */
    onSearchField:function (field) {
        var grid = this.ownerCt.ownerCt.view,
                store = grid.store;

        if (this.value.length == 0) {
            store.clearFilter();
            return false;
        }

        store.filter('headline', this.value);
        return true;
    },

    deleteSingleUser:function () {
    }
});