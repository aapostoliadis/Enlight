/**
 * Shopware UI - Vertical Scrolling
 *
 * This is a helper class which provides
 * smooth scrolling. The class uses CSS3
 * transitions if there are supported in
 * the user's browser.
 *
 * Note that this class needs our viewport
 * class to work proper.
 *
 * @singleton
 * @author: s.pohl <stp@shopware.de>
 * @date: 2011-11-23
 */
Ext.define('Shopware.app.VerticalScroll', {
	singleton: true,

	/** Contains the application viewport */
	viewport: null,

	/** Animation duration */
	duration: 300,

	/** Animation easing method */
	easing: 'ease',

	/** Array of all opened Ext.window.Window */
	openWindows: [],

	/**
	 * Main method of this class which handles the scrolling
	 *
	 * @param direction - scroll direction e.g. left or right
	 */
	scroll: function(direction) {
		var me = this,
			body = this.viewport || Ext.getBody().parent(),
			scrollWidth = (direction == 'right') ? Ext.Element.getViewportWidth() : 0;

		body.animate({
			duration: this.duration,
			easing: this.easing,
			to: {
				scrollLeft: scrollWidth
			}
		});

		Shopware.app.Application.activeDesktop = (direction == 'right') ? 1 : 0;
	},

    /**
     * Allows to jump to a specific desktop based on the passed
     * desktop index
     *
     * @param index - index of the desktop (starting from 1)
     * @param duration - animation duration
     * @param easing - easing method
     */
    jumpTo: function(index, duration, easing) {
        var me = this,
            body = this.viewport || Ext.getBody().parent(),
            scrollWidth = Ext.Element.getViewportWidth * index,
            duration = duration || 0,
            easing = easing || this.easing;

        console.log(duration);

        body.animate({
            duration: duration,
            easing: easing,
            to: {
                scrollLeft: scrollWidth
            }
        })
    }
});