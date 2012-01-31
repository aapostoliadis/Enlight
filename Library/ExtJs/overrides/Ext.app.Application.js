/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_ExtJs
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Stephan P.
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * Override the default ext application
 * to add our sub application functionality
 *
 * @category   Enlight
 * @package    Enlight_ExtJs
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
Ext.override(Ext.app.Application, {

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
    },

	/**
	 * Adds a new controller to the application
	 *
	 * @param controller
	 * @param skipInit
	 */
	addController: function(controller, skipInit) {

		if (Ext.isDefined(controller.name)) {
			var name = controller.name;
			delete controller.name;

			controller.id = controller.id || name;

			controller = Ext.create(name, controller);
		}

		var me          = this,
			controllers = me.controllers;

		controllers.add(controller);


		if (!skipInit) {
			controller.init();
		}

		return controller;
	},

	/**
	 * Remove a controller from the application
	 *
	 * @param controller
	 * @param removeListeners
	 */
	removeController: function(controller, removeListeners) {
		removeListeners = removeListeners || true;

		var me          = this,
			controllers = me.controllers;

		controllers.remove(controller);

		if (removeListeners) {
			var bus = me.eventbus;

			bus.uncontrol([controller.id]);
		}
	},

	/**
	 * Adds a new sub application to the
	 * main application
	 *
	 * @param subapp
	 */
	addSubApplication: function(subapp) {

		subapp.app = this;


		this.addController(subapp, true);

		return subapp;
	}
});

/**
 * Custom Ext.loader getPath method
 *
 * This bends the controller/model/store/view paths
 * to our shopware default paths.
 *
 * @param className
 * @return path
 */
Ext.Loader.getPath = function(className) {
	var tempClass = className,
		path = '',
		paths = this.config.paths,
		prefix = this.getPrefix(className),
		suffix = this.config.suffixes[prefix] !== undefined ? this.config.suffixes[prefix] : '.js';

	if (prefix.length > 0) {
		if (prefix === className) {
			return paths[prefix];
		}

		path = paths[prefix];
		className = className.substring(prefix.length + 1);
	}

	if (path.length > 0) {
        path = path.replace(/\/+$/, '') + '/';
	}

	return path.replace(/\/\.\//g, '/') + className.replace(/\./g, "/") + suffix;
};

Ext.Loader.config.disableCaching = false;

/**
 * Sets the path of a namespace. For Example:
 *
 *     Ext.Loader.setPath('Ext', '.');
 *
 * @param {String/Object} name See {@link Ext.Function#flexSetter flexSetter}
 * @param {String} path See {@link Ext.Function#flexSetter flexSetter}
 * @return {Ext.Loader} this
 * @method
 */
Ext.Loader.setPath = function(name, path, suffix) {
    this.config.paths[name] = path;
    if(this.config.suffixes === undefined) {
        this.config.suffixes = [];
    }
    if(suffix !== undefined) {
        this.config.suffixes[name] = suffix;
    }
    return this;
};