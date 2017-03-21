/**
 * Scripts for Fusion Video Extension
 */
 
//videos in modals
jQuery(document).ready(function() {
	//pause video on modal close
	jQuery('.modal').on('hide.bs.modal', function(e) {
		var modal = jQuery(this);
		if (modal.find('.fsn-video.youtube').length > 0) {
			//YouTube
			var modalID = modal.attr('id');
			var target = document.getElementById(modalID);
		    var iframe = target.getElementsByTagName("iframe")[0].contentWindow;		    
			iframe.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
		} else if (modal.find('.fsn-video.vimeo').length > 0) {
			//Vimeo
			var modalID = modal.attr('id');
			var target = document.getElementById(modalID);
		    var iframe = target.getElementsByTagName("iframe")[0].contentWindow;
			iframe.postMessage('{"method":"pause"}', '*');
		} else if (modal.find('.fsn-video.self_hosted').length > 0) {
			//Self Hosted
			var videoID = modal.find('.video-js').attr('id');
			var videoPlayer = videojs(videoID);
			videoPlayer.pause();
		}
	});
});