var storyboard = {
	window: {},
	storyboard: {},
	panels: {},
	panelImages: {},
	totalPanels: 0,
	currentSlide: 0,
	advanceSlideNum: 2,
	visible: false,
	loading: {},
	animateTime: 2000,
	animateEasing: 'linear',
	pagination: {},
	init: function () {
		'use strict';
		this.window = jQuery(window);
		this.storyboard = jQuery('#wpstoryboardgallery');
		this.panels = this.storyboard.children('div');
		this.panelImages = this.panels.children('img');
		this.totalPanels = Math.ceil(this.panels.length / this.advanceSlideNum);
		this.window.resize(function () {
			storyboard.resize();
		});
		this.pagination = jQuery("#wpstoryboardgallery_pagination");
		jQuery('#wpstoryboardgallery img, #wpstoryboardgallery h2').on('click', function (e) {
			var $obj = jQuery(this),
				id = $obj.data('id'),
				link = $obj.parent().data('url') || false,
				direction = Math.ceil(id / storyboard.advanceSlideNum) - storyboard.currentSlide;
			if (link !== false) {
				window.location = link;
			} else {
				if (direction === 0) {
					storyboard.prev();
				} else {
					storyboard.next();
				}
			}
			return false;
		});
		jQuery(document).bind('keydown', function (e) {
			if (storyboard.storyboard.visible) {
				if (e.keyCode === 39 || e.keyCode === 32) {
					storyboard.next();
				}
				if (e.keyCode === 37) {
					storyboard.prev();
				}
				if (e.keyCode === 27) {
					storyboard.hide();
				}
			}
		});
		jQuery('.wpstoryboardgallery_close').on('click', function () {
			storyboard.hide();
			return false;
		});
		this.pagination.pagination(this.totalPanels, {
		    items_per_page: 1,
		    load_first_page: false,
		    next_text: '&raquo;',
		    prev_text: '&laquo;',
		    num_edge_entries: 2,
		    callback: function (new_page_index, pagination_container) {
		    	console.log(new_page_index);
		        storyboard.slideTo(new_page_index);
		        return false;
		    }
		});
	},
	show: function () {
		'use strict';
		this.storyboard.visible = true;
		this.storyboard.parent().fadeIn(function () {
			var getcompstyle = window.getComputedStyle || false,
				size = 'full';
			if (getcompstyle !== false) {
				size = getcompstyle(document.body, ':after').getPropertyValue('content').replace(/\"/gi, '');
			}
			storyboard.panelImages.each(function () {
				var obj = jQuery(this),
					newsrc = obj.data(size);
				obj.attr('src', newsrc);
				setTimeout(function () { obj.removeClass('lazyload'); }, 500);
			});
		});
		storyboard.resize(); 
				
	},
	resize: function (firstrun) {
		'use strict';
		var imgWidth = this.panelImages.attr('width'),
			imgHeight = this.panelImages.attr('height'),
			windowWidth = this.window.width(),
			windowHeight = this.window.height(),
			marginTop = 0,
			slidePixels = ((this.window.width() / 2) * this.advanceSlideNum * this.currentSlide * -1);

		imgHeight = (windowWidth / 2) / (imgWidth / imgHeight);
		imgWidth = windowWidth / 2;
		marginTop = (windowHeight - imgHeight) / 2;

		this.panelImages.width(imgWidth);
		this.panelImages.height(imgHeight);
		this.storyboard.css({ 'margin-top': marginTop + 'px' });
		this.storyboard.stop(true).css({'left': slidePixels + 'px' });
	},
	hide: function () {
		'use strict';
		this.storyboard.parent().fadeOut();
		this.storyboard.visible = false;
	},
	slideTo: function (slide) {
		'use strict';
		var slidePixels = ((this.window.width() / 2) * this.advanceSlideNum * slide * -1);
		this.storyboard.stop(true).animate({'left': slidePixels + 'px' }, this.animateTime, this.animateEasing);
		this.currentSlide = slide;
	},
	next: function () {
		'use strict';
		this.pagination.trigger('nextPage');
	},
	prev: function () {
		'use strict';
		this.pagination.trigger('prevPage');
	}
};