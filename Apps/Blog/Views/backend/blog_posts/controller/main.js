Ext.define('Enlight.apps.BlogPosts.controller.Main', {
    extend:'Ext.app.Controller',
    views:[ 'main.List', 'main.Create', 'main.Edit' ],
    stores:[ 'Main' ],
    models:[ 'Main' ],

    refs:[
        { ref:'grid', selector:'grid' },
        { ref:'addBtn', selector:'button[action=add]' },
        { ref:'saveBtn', selector:'window button[action=save]' }
    ],

    /**
     * Creates the neccessary event listener for this
     * specific controller and opens a new Ext.window.Window
     * to display the subapplication
     */
    init:function () {
        var me = this;

        this.control({
            'grid button[action=add]':{
                click:this.onCreatePost
            },
            'window button[action=save]':{
                click:this.onSaveUser
            },
            'postedit button[action=save]':{
                click:this.editPost
            },
            'grid button[action=deletePosts]':{
                click:this.deleteMultiplePosts
            },
            'grid button[action=deleteUser]':{
                click:this.deleteSingleUser
            },
            'grid actioncolumn':{
                render:function (view) {
                    view.scope = me;
                    view.handler = me.handleActionColumn;
                }
            }
        });

        Ext.create('Ext.window.Window', {
            layout:'border',
            width:800,
            height:600,
            maximizable:true,
            border:0,
            title:'Blog Post Administration',
            items:[ this.getView('main.List') ]
        }).show();
    },

    /**
     * Helper method which handles all clicks of the action column
     *
     * @param [object] view
     * @param [integer] rowIndex
     * @param [integer] colIndex
     * @param [object] item
     * @return void
     */
    handleActionColumn:function (view, rowIndex, colIndex, item) {

        switch (item.iconCls) {
            case 'pencil':
                this.onEditPost(view, item, rowIndex);
                break;
            case 'delete':
                this.deleteSingleUser(view, rowIndex);
                break;
            default:
                break;
        }
    },

    /**
     * Opens the Ext.window.window which displays
     * the Ext.form.Panel to create a new user
     */
    onCreatePost:function () {
        Ext.widget('postcreate');
    },

    /**
     * Event listener method which handles the edit user action column button
     *
     * @param [object] view
     * @param [object] item
     * @param [integer] rowIndex
     * @return void
     */
    onEditPost:function (view, item, rowIndex) {
        var store = view.getStore(),
                record = store.getAt(rowIndex);
        Ext.widget('postedit', { record:record });
    },

    /**
     * Event listener method which adds a user
     * @param button
     */
    onSaveUser:function (button) {
        var win = button.up('window'),
                form = win.down('form'),
                values = form.getValues(),
                blogPost = Ext.create('Enlight.apps.BlogPosts.model.Main', values),
                store = this.getStore('Main'),
                grid = this.getGrid();

        grid.setLoading(true);
        blogPost.save({
            success:function () {
                grid.setLoading(false);
                store.add(blogPost);
                win.close();
                store.load();
            }
        });
    },

    /**
     * Event listener method which deletes multiple users
     */
    deleteMultiplePosts:function () {

        var grid = this.getGrid(),
                sm = grid.getSelectionModel(),
                selection = sm.selected.items,
                store = this.getStore('Main');

        Ext.MessageBox.confirm('Delete marked posts', 'Do you really want to delete all marked posts', function (response) {
            if (response == 'yes') {
                grid.setLoading(true);
                Ext.each(selection, function (post) {
                    var userModel = Ext.create('Enlight.apps.BlogPosts.model.Main', post.data);
                    userModel.destroy({
                        success:function () {
                            store.remove(userModel);
                        }
                    });
                });

                store.load();
                grid.setLoading(false);
            } else {
                return false;
            }
        })
    },
    /**
     * Event listener which updates the user informations
     *
     * @param [object] button
     * @return void
     */
    editPost:function (button) {
        var win = button.up('window'),
                form = win.down('form'),
                values = form.getValues(),
                record = form.getRecord(),
                grid = this.getGrid();

        record.set(values);
        grid.setLoading(true);
        record.save({
            success:function () {
                grid.setLoading(false);
                win.close();
            }
        });
    }
});