/**
 * Override the default ext application
 * to add our sub application functionality
 *
 */
Ext.override(Ext.app.Controller, {
    /**
     * Returns instance of a {@link Ext.app.Controller controller} with the given name.
     * When controller doesn't exist yet, it's created.
     * @param {String} name
     * @return {Ext.app.Controller} a controller instance.
     */
    getController: function(name) {
        return this.subApplication.getController(name);
    },

    /**
     * Returns instance of a {@link Ext.data.Store Store} with the given name.
     * When store doesn't exist yet, it's created.
     * @param {String} name
     * @return {Ext.data.Store} a store instance.
     */
    getStore: function(name) {
        return this.subApplication.getStore(name);
    },

    /**
     * Returns a {@link Ext.data.Model Model} class with the given name.
     * A shorthand for using {@link Ext.ModelManager#getModel}.
     * @param {String} name
     * @return {Ext.data.Model} a model class.
     */
    getModel: function(model) {
        return this.subApplication.getModel(model);
    },

    /**
     * Returns a View class with the given name.  To create an instance of the view,
     * you can use it like it's used by Application to create the Viewport:
     *
     *     this.getView('Viewport').create();
     *
     * @param {String} name
     * @return {Ext.Base} a view class.
     */
    getView: function(view) {
        return this.subApplication.getView(view);
    },

    onClassExtended: function(cls, data) {
        var className = Ext.getClassName(cls),
            match = className.match(/^(.*)\.controller\./);

        if (match !== null) {
            var namespace = match[1] || Ext.Loader.getPrefix(className),
                onBeforeClassCreated = data.onBeforeClassCreated,
                requires = [],
                modules = ['model', 'view', 'store'],
                prefix;

            data.onBeforeClassCreated = function(cls, data) {
                var i, ln, module,
                    items, j, subLn, item;

                for (i = 0,ln = modules.length; i < ln; i++) {
                    module = modules[i];

                    items = Ext.Array.from(data[module + 's']);

                    for (j = 0,subLn = items.length; j < subLn; j++) {
                        item = items[j];

                        prefix = Ext.Loader.getPrefix(item);
                        if (prefix === '' || prefix === item) {
                            requires.push(namespace + '.' + module + '.' + item);
                        }
                        else {
                            requires.push(item);
                        }
                    }
                }
                Ext.require(requires, Ext.Function.pass(onBeforeClassCreated, arguments, this));
            };
        }
    }
});