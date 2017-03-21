/**
 * Scripts for Fusion Menu Extension
 */

//Nav Menu

//multi-column dropdown
jQuery(document).ready(function() {
	//columns and sizing
	jQuery('.multi-col-nav-container').each(function() {
		var scrollMenu = jQuery('#scroll-menu');
		var nav = jQuery(this);
		var cols = nav.find('.multi-col-nav');
		var numCols = cols.length;
		var colSpan = 12 / numCols;
		scrollMenu.addClass('loading');
		nav.addClass('loading');
		if (numCols > 1) {
			var navWidth = nav.outerWidth();
			var gutterPadding = nav.children('.container-fluid').css('padding-left');
			var gutterWidth = parseInt(gutterPadding) * (numCols * 2);
			var multiColWidth = (navWidth * numCols) + (gutterWidth * (numCols - 1));
			nav.css('width', multiColWidth +'px');
		}
		scrollMenu.removeClass('loading');
		nav.removeClass('loading');
		nav.addClass('numcols-'+ numCols);
		cols.addClass('col-sm-'+colSpan);
		nav.trigger('fusionMainMenuBuilt', navWidth);
	});
	//top level hashtag links
	jQuery('.fsn-menu.main .navbar-nav > li > a').on('click', function(e) {
		if (jQuery(this).attr('href') == '#') {
			e.preventDefault();
		}
	});
});

//toggle vs hover
jQuery(document).ready(function() {
	if (!Modernizr.touch) {
		var dropdownToggles = jQuery('.fsn-menu.main').find('.dropdown-toggle');
		dropdownToggles.each(function() {
			var dropdownToggle = jQuery(this);
			dropdownToggle.removeAttr('data-toggle');
			dropdownToggle.dropdownHover();
		});
	}
});

//more descriptive collapsing classes
jQuery(document).ready(function() {
	var navbarCollapses = jQuery('.fsn-menu .navbar-collapse');
	navbarCollapses.each(function() {
		var navbarCollapse = jQuery(this);
		var navbar = navbarCollapse.parents('.fsn-menu');
		navbarCollapse.on('show.bs.collapse', function(e) {
			navbarCollapse.addClass('collapsing-in');
			navbar.addClass('expanded');
		});
		navbarCollapse.on('shown.bs.collapse', function(e) {
			navbarCollapse.removeClass('collapsing-in');	
		});
		navbarCollapse.on('hide.bs.collapse', function(e) {
			navbarCollapse.addClass('collapsing-out');	
			navbar.removeClass('expanded');
		});
		navbarCollapse.on('hidden.bs.collapse', function(e) {
			navbarCollapse.removeClass('collapsing-out');	
		});
	});
});