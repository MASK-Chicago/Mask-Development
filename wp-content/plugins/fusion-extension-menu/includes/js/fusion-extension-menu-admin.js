/**
 * WP Admin scripts for Menu extension
 */

//init menu
jQuery(document).ready(function() {
	jQuery('body').on('show.bs.modal', '#fsn_menu_modal', function(e) {
		var menu = jQuery('#fsn_menu_modal');
		var selectLayoutElement = jQuery('[name="menu_layout"]');
		var selectedLayout = selectLayoutElement.val();
		
		menu.attr('data-layout', selectedLayout);
	});
});

//update menu function
jQuery(document).ready(function() {
	jQuery('body').on('change', 'select[name="menu_layout"]', function(e) {
		fsnUpdateMenu(e);
	});
});

function fsnUpdateMenu(event) {
	var selectLayoutElement = jQuery(event.target);		
	var selectedLayout = selectLayoutElement.val();
	var menu = jQuery('#fsn_menu_modal');
	var currentLayout = menu.attr('data-layout');
	if (currentLayout != '' && currentLayout != selectedLayout) {
		var r = confirm(fsnExtMenuL10n.layout_change);
		if (r == true) {			
			menu.attr('data-layout', selectedLayout);
			fsnUpdateMenuLayout();
		} else {
			selectLayoutElement.find('option[value="'+ currentLayout +'"]').prop('selected', true);
		}
	} else {
		menu.attr('data-layout', selectedLayout);
		fsnUpdateMenuLayout();
	}
}

//update menu layout
function fsnUpdateMenuLayout() {
	var postID = jQuery('input#post_ID').val();
	var menuLayout = jQuery('[name="menu_layout"]').val();
	
	var data = {
		action: 'menu_load_layout',
		menu_layout: menuLayout,
		post_id: postID,
		security: fsnExtMenuJS.fsnEditMenuNonce
	};
	jQuery.post(ajaxurl, data, function(response) {
		if (response == '-1') {
			alert(fsnExtMenuL10n.error);
			return false;
		}
		
		jQuery('#fsn_menu_modal .tab-pane .form-group.menu-layout').remove();
		if (response !== null) {
			jQuery('#fsn_menu_modal .tab-pane').each(function() {
				var tabPane = jQuery(this);
				if (tabPane.attr('data-section-id') == 'general') {
					tabPane.find('.form-group').first().after('<div class="layout-fields"></div>');
				} else {
					tabPane.prepend('<div class="layout-fields"></div>');
				}
			});
			for(i=0; i < response.length; i++) {
				jQuery('#fsn_menu_modal .tab-pane[data-section-id="'+ response[i].section +'"] .layout-fields').append(response[i].output);
			}
			jQuery('#fsn_menu_modal .tab-pane').each(function() {
				var tabPane = jQuery(this);
				tabPane.find('.menu-layout').first().unwrap();
				tabPane.find('.layout-fields:empty').remove();
				//toggle panel tabs visibility
				var tabPaneId = tabPane.attr('id'); 
				if (tabPane.is(':empty')) {
					jQuery('a[data-toggle="tab"][href="#'+ tabPaneId +'"]').parent('li').hide();
				} else {
					jQuery('a[data-toggle="tab"][href="#'+ tabPaneId +'"]').parent('li').show();
				}
			});
		}
		var modalSelector = jQuery('#fsn_menu_modal');
		//reinit tinyMCE
		if (jQuery('#fsncontent').length > 0) {
			//make compatable with TinyMCE 4 which is used starting with WordPress 3.9
			if(tinymce.majorVersion === "4") {
				tinymce.execCommand('mceRemoveEditor', true, 'fsncontent');
            } else {
				tinymce.execCommand("mceRemoveControl", true, 'fsncontent');
            }
			var $element = jQuery('#fsncontent');
	        var qt, textfield_id = $element.attr("id"),
	            content = '';
	
	        window.tinyMCEPreInit.mceInit[textfield_id] = _.extend({}, tinyMCEPreInit.mceInit['content']);
	
	        if(_.isUndefined(tinyMCEPreInit.qtInit[textfield_id])) {
	            window.tinyMCEPreInit.qtInit[textfield_id] = _.extend({}, tinyMCEPreInit.qtInit['replycontent'], {id: textfield_id})
	        }
	        //$element.val($content_holder.val());
	        qt = quicktags( window.tinyMCEPreInit.qtInit[textfield_id] );
	        QTags._buttonsInit();
	        //make compatable with TinyMCE 4 which is used starting with WordPress 3.9
	        if(tinymce.majorVersion === "4") tinymce.execCommand( 'mceAddEditor', true, textfield_id );
	        window.switchEditors.go(textfield_id, 'tmce');
	        //focus on this RTE
	        tinyMCE.get('fsncontent').focus();
			//destroy tinyMCE
			modalSelector.on('hidden.bs.modal', function() {					
				//make compatable with TinyMCE 4 which is used starting with WordPress 3.9
				if(tinymce.majorVersion === "4") {
					tinymce.execCommand('mceRemoveEditor', true, 'fsncontent');
                } else {
					tinymce.execCommand("mceRemoveControl", true, 'fsncontent');
                }
			});
		}
		//initialize color pickers
		jQuery('.fsn-color-picker').wpColorPicker();
		//set dependencies
		setDependencies(modalSelector);
		//trigger item added event
		jQuery('body').trigger('fsnMenuUpdated');
	});	
}

//For select2 fields inside menu items
jQuery(document).ready(function() {	
	jQuery('body').on('fsnMenuUpdated', function(e) {
		fsnInitPostSelect();
	});
});