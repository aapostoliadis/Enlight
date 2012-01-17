/**
 * Shopware UI - Default tab override
 *
 * Overrides the Ext.tab.Tab to provide
 * an additional HTML5 data attribute to provide
 * a better adressing in selenium ui tests.
 *
 * @author: s.pohl <stp@shopware.de>
 * @date: 2011-12-13
 */
Ext.override(Ext.tab.Tab, {

	/** Suffix fpr the data attribute */
	dataSuffix: 'card',

	afterRender: function() {
		var me = this;
        me.callOverridden(me);

        if(me.card && me.card.action) {
            var dom = me.getEl().dom.children[0].children[0];
            dom.setAttribute('data-' + me.dataSuffix, me.card.action);
        }
	}
})