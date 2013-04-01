/*
 * @name DoubleScroll
 * @desc displays scroll bar on top and on the bottom of the div
 * @requires jQuery, jQueryUI
 *
 * @author Pawel Suwala - http://suwala.eu/
 * @version 0.2 (07-06-2012)
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */

(function($){
    $.widget("suwala.doubleScroll", {
		options: {
            contentElement: undefined, // Widest element, if not specified first child element will be used
            scrollElement: undefined,
			topScrollBarMarkup: '<div class="suwala-doubleScroll-scroll-wrapper" style="height: 20px;"><div class="suwala-doubleScroll-scroll" style="height: 20px;"></div></div>',
			topScrollBarInnerSelector: '.suwala-doubleScroll-scroll',
			scrollCss: {
				'overflow-x': 'auto',
				'overflow-y':'hidden'
            },
			contentCss: {
				'overflow-x': 'hidden',
				'overflow-y':'hidden'
			}
        },
        _create : function() {
            var self = this;
			var contentElement;
			var scrollElement;

            // add div that will act as an upper scroll
			var topScrollBar = $($(self.options.topScrollBarMarkup));
            self.element.before(topScrollBar);

            // find the content element (should be the widest one)
            if (self.options.contentElement !== undefined && self.element.find(self.options.contentElement).length !== 0) {
                contentElement = self.element.find(self.options.contentElement);
            }
            else {
                contentElement = self.element.find('>:first-child');
            }

             // find the scroll element (should be the widest one)
            if (self.options.scrollElement !== undefined && self.element.find(self.options.scrollElement).length !== 0) {
                scrollElement = self.element.find(self.options.scrollElement);
            }
            else {
                scrollElement = self.element.find('.dataTables_scrollBody');
            }

            // bind upper scroll to bottom scroll
            topScrollBar.scroll(function(){
                scrollElement.scrollLeft(topScrollBar.scrollLeft());
            });

            // bind bottom scroll to upper scroll
            scrollElement.scroll(function(){
                topScrollBar.scrollLeft(scrollElement.scrollLeft());
            });

            // apply css
            topScrollBar.css(self.options.scrollCss);
            self.element.css(self.options.contentCss);

            // set the width of the wrappers
            $(self.options.topScrollBarInnerSelector, topScrollBar).width(contentElement.outerWidth());
			// topScrollBar.width(self.element.width());
			
			// resize de la topScrollBar 
			var body_height = $('#table_products_wrapper').find('tbody').height();
			var table_height = $('#table_products_wrapper').height();
			if(body_height >= table_height) topScrollBar.width($('#table_products_wrapper').width());	
            
        }
    });
})(jQuery);