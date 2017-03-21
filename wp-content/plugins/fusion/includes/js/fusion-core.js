/**
 * Front-end Scripts.
 *
 * Scripts for Front-end pages.
 *
 * @since 1.0.0
 *
 * @package Fusion
 */
 
//Button collapse text toggle
jQuery(document).ready(function() {
	var buttonCollapseToggles = jQuery('a[data-toggle="collapse"][data-label-show][data-label-hide]');
	buttonCollapseToggles.each(function() {
		var button = jQuery(this);
		if (button.children().length == 0) {
			var target = button.attr('href');
			var targetObject = jQuery(target);
			var labelShow = button.attr('data-label-show');
			var labelHide = button.attr('data-label-hide');
			targetObject.on('show.bs.collapse', function () {
				button.text(labelHide);
			});
			targetObject.on('hide.bs.collapse', function () {
				button.text(labelShow);
			});
		}
	});
});
 
//set images
jQuery(window).load(function() {
	var size = jQuery('body').attr('data-view');
	ADimageSwap(size);
});

 
/**
 * Tabs
 */

//set resize event on tab change
jQuery(document).ready(function() {
	var tabShowInterval;
	jQuery('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
		tabShowInterval = setInterval(function(){
			jQuery(window).trigger('resize');
		},10);
	});
	jQuery('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		setTimeout(function(){
			clearInterval(tabShowInterval);
		}, 1000);
	});
});
 
/**
 * JavaScript Media Queries
 */

//initialize enquire.js and necessary polyfills
jQuery(document).ready(function() {
	Modernizr.load([
	    //load matchmedia polyfill if needed
	    {
	        test: Modernizr.matchmedia,
	        nope: fsnAjax.pluginurl + "includes/js/media.match.min.js",
	        complete: function() {
	        	fsnInitEnquireJS();
	        }
	    }
	]);	
});

function fsnInitEnquireJS() {

if (typeof enquire != 'function') { 
/*!
 * enquire.js v2.1.0 - Awesome Media Queries in JavaScript
 * Copyright (c) 2013 Nick Williams - http://wicky.nillia.ms/enquire.js
 * License: MIT (http://www.opensource.org/licenses/mit-license.php)
 */

(function(t,i,n){var e=i.matchMedia;"undefined"!=typeof module&&module.exports?module.exports=n(e):"function"==typeof define&&define.amd?define(function(){return i[t]=n(e)}):i[t]=n(e)})("enquire",this,function(t){"use strict";function i(t,i){var n,e=0,s=t.length;for(e;s>e&&(n=i(t[e],e),n!==!1);e++);}function n(t){return"[object Array]"===Object.prototype.toString.apply(t)}function e(t){return"function"==typeof t}function s(t){this.options=t,!t.deferSetup&&this.setup()}function o(i,n){this.query=i,this.isUnconditional=n,this.handlers=[],this.mql=t(i);var e=this;this.listener=function(t){e.mql=t,e.assess()},this.mql.addListener(this.listener)}function r(){if(!t)throw Error("matchMedia not present, legacy browsers require a polyfill");this.queries={},this.browserIsIncapable=!t("only all").matches}return s.prototype={setup:function(){this.options.setup&&this.options.setup(),this.initialised=!0},on:function(){!this.initialised&&this.setup(),this.options.match&&this.options.match()},off:function(){this.options.unmatch&&this.options.unmatch()},destroy:function(){this.options.destroy?this.options.destroy():this.off()},equals:function(t){return this.options===t||this.options.match===t}},o.prototype={addHandler:function(t){var i=new s(t);this.handlers.push(i),this.matches()&&i.on()},removeHandler:function(t){var n=this.handlers;i(n,function(i,e){return i.equals(t)?(i.destroy(),!n.splice(e,1)):void 0})},matches:function(){return this.mql.matches||this.isUnconditional},clear:function(){i(this.handlers,function(t){t.destroy()}),this.mql.removeListener(this.listener),this.handlers.length=0},assess:function(){var t=this.matches()?"on":"off";i(this.handlers,function(i){i[t]()})}},r.prototype={register:function(t,s,r){var h=this.queries,u=r&&this.browserIsIncapable;return h[t]||(h[t]=new o(t,u)),e(s)&&(s={match:s}),n(s)||(s=[s]),i(s,function(i){h[t].addHandler(i)}),this},unregister:function(t,i){var n=this.queries[t];return n&&(i?n.removeHandler(i):(n.clear(),delete this.queries[t])),this}},new r});

}
	
	//desktop version
	enquire.register("screen and (min-width: 768px)", {
		match : function() {			
			//desktop functions go here
			setViewport('desktop');
		},
		unmatch : function() {
			//mobile functions go here
			setViewport('mobile');
		}
	});	

}

//add body attribute hook
function setViewport(size) {
	jQuery('body').attr('data-view',size);
	ADimageSwap(size);
}

//update images
function ADimageSwap(size) {
	//create json object
	var $images = jQuery('.ad-dynamic-image');
	$images.each(function() {		
		var image = jQuery(this);
		var imageData = image.data('image-json');
		switch(size) {
			case 'mobile':
				var imageSrc = imageData.mobile_src;
				var imageWidth = imageData.mobile_width;
				var imageHeight = imageData.mobile_height;
				break;
			case 'desktop':
				var imageSrc = imageData.desktop_src;
				var imageWidth = imageData.desktop_width;
				var imageHeight = imageData.desktop_height;
				break;
		}
		image.attr('src', imageSrc);
		image.attr('width', imageWidth);
		image.attr('height', imageHeight);
		jQuery(window).imagesLoaded(function() {
			jQuery('body').trigger('imagesSwapped.fsn');
		});
	});	
}