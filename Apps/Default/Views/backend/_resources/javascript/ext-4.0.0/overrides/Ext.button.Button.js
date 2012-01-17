/**
 * Shopware UI - Default button override
 *
 * Overrides the Ext.button.Button to provide
 * an additional HTML5 data attribute to provide
 * a better adressing in selenium ui tests.
 *
 * @author: s.pohl <stp@shopware.de>
 * @date: 2011-12-07
 */
Ext.override(Ext.button.Button, {

    /** Suffix for the data attribute */
    dataSuffix: 'action',

    afterRender: function() {
        var me = this;
        me.callOverridden(me);

        if(me.action) {
            var dom = me.getEl().dom.children[0].children[0];
            dom.setAttribute('data-' + me.dataSuffix, me.action);
        }
    }
})