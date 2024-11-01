(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 *
	*/

	$( window ).load(function() {
		/**
		 * Datepickers
		 * 
		*/ 
		var datepickerConfiguration = {
			dateFormat: "dd-mm-yy",
			maxDate: new Date(),
			changeYear: true,
		}

		$('#media-attachment-date-filters-from, #media-attachment-date-filters-to').datepicker(datepickerConfiguration);

		/**
		 * Selectize multi options
		 * 
		*/ 
		$('.smt-selectize-multiple').selectize({

		});

		
		jQuery('.smt-selectize-multiple-add-first').selectize({
			placeholder: "Start type...",
		});	

		/**
		 * WP media modal
		 *
		*/
		$("#smt-add-media-btn").on("click",  function (e) {
			e.preventDefault();
			var $link = $(this);
	
			// Create the media frame.
			let file_frame = wp.media.frames.file_frame = wp.media({
				title: 'Select or upload image',

				button: {
					text: 'Select'
				},
				multiple: true  // Set to true to allow multiple files to be selected
			});
		
			// When an image is selected, run a callback.
			file_frame.on('select', function () {
				let gallery_ids = {};
				var attachments = file_frame.state().get('selection').toJSON();
				attachments.forEach(element => {
					console.log(element);
					if(element.type === 'image') {
						$('.smt-media-list').prepend('<li class="smt-media-list-li" data-id="'+element.id+'" onclick="smet_image_click('+element.id+', this);"><div class="smt-media-img-wrapper"><img src="'+element.sizes.thumbnail.url+'" class="attachment-thumbnail size-thumbnail" alt="" loading="lazy" width="150" height="150"/></div></li>');
						// $('.smt-media-list').children('.smt-media-list-li').last().remove();
						// smet_add_only_media_cat(element.id, element.sizes.thumbnail.url);
						gallery_ids[element.id] = element.sizes.thumbnail.url;
					}
					else {
						$('.smt-media-list').prepend('<li class="smt-media-list-li" data-id="'+element.id+'" onclick="smet_image_click('+element.id+', this);"><div class="smt-media-img-wrapper smt-img-wrapper-files-icon"><img src="'+element.icon+'" class="attachment-thumbnail size-thumbnail" alt="" loading="lazy"/><div>'+ element.filename +'</div></div></li>');
						// $('.smt-media-list').children('.smt-media-list-li').last().remove();
						// smet_add_only_media_cat(element.id, element.icon);
						gallery_ids[element.id] = element.icon;
					}
				});		
				smet_add_only_media_cat(gallery_ids);
			});
			file_frame.open();
		});
		
		$( ".smt-modal-close" ).click(function() {
			$(".smt-modal").css("display", "none");
		});

		/**
		 * Show default media
		 * 
		*/ 
		$("#smt-filter-media-btn").on("click",  function (e) {
			smet_load_filtered_media(1);
		});

		$("#smt-filter-media-btn").click();

		/**
		 * Show add new Media Category modal
		 * 
		*/
		$("#smt-add-media-category-btn").on("click",  function (e) {
			$('#smt-new-cat-textfield').val('');	
			$("#smt-modal-add-cat").css("display", "block");
		});

		/**
		 * Save new Media Category
		 * 
		*/
		$("#smt-add-new-media-category").on("click",  function (e) {
			let new_media_category = $(this).closest('.smt-modal-inner-content-add').find('#smt-new-cat-textfield').val();
			$.ajax({
				url: smet_save_new_media_category.ajax_url,
				type: 'POST',
				data: {
					'new-media-category': new_media_category,
					'action': 'smet_save_new_media_category'
				},
				beforeSend: function() {
					smet_show_loader();
				},
				success: function(data) {
					if(data.success)
					{
						smet_show_success_pop_up('Media category saved!');
						$(".smt-media-cat-table tbody").append(data.data.html);
						$(".smt-modal").css("display", "none");
					}
					else {
						smet_show_alert_pop_up(data.data.description);
					}
					$('#smt-new-cat-textfield').val('');
					smet_hide_loader();	
				},
			});
		});

		/**
		 * Edit existent Media Category
		 * 
		*/
		$("#smt-edit-media-category").on("click",  function (e) {
			let cat_id = $(this).closest('.smt-modal-inner-content-add').find('#smt-edit-cat-id').val();
			let edit_new_name = $(this).closest('.smt-modal-inner-content-add').find('#smt-edit-cat-textfield').val();
			$.ajax({
				url: smet_edit_existent_media_category.ajax_url,
				type: 'POST',
				data: {
					'new-name-category': edit_new_name,
					'cat-id': cat_id,
					'action': 'smet_edit_existent_media_category'
				},
				beforeSend: function() {
					smet_show_loader();
				},
				success: function(data) {
					if(data.success)
					{
						smet_show_success_pop_up('Sucessfully edited!');
						$('#'+data.data.row_id+ ' .smt-table-name').html(data.data.new_name);
						$(".smt-modal").css("display", "none");
					}
					else {
						smet_show_alert_pop_up(data.data.description);
					}
					$('#smt-edit-cat-textfield').val('');
					smet_hide_loader();	
				},
			});
		});

		/**
		 * Multiple Selection class
		 * 
		*/
		$("#smt-multiple-select-media-btn").on("click", function() {
			$('.smt-media-list-li img').toggleClass('smt-to-select');
			$('.smt-main-actions').hide();
			$('.smt-delete-actions').show();
			$('.smt-edit-bulk-actions').show();
			$('#smt-multiple-select-back-media-btn').show();

		});

		$("#smt-multiple-select-back-media-btn").on("click", function() {
			$('.smt-main-actions').show();
			$('.smt-delete-actions').hide();
			$('.smt-edit-bulk-actions').hide();
			$('#smt-multiple-select-back-media-btn').hide();
			$('.smt-media-list-li img').toggleClass('smt-to-select');
			var act = $('.smt-media-list-li img').hasClass("smt-to-select");
			
			if(!act) {
				$('.smt-selected-to-delete').removeClass("smt-selected-to-delete");
				$('.smt-main-actions').show();
				$('.smt-delete-actions').hide();
				$('#smt-multiple-select-back-media-btn').hide();
				$('.smt-edit-bulk-actions').hide();
				$('#smt-ids-to-bulk').val('');
			}
		});

		/**
		 * Popups closing btn and timeout
		 * 
		*/
		var close = document.getElementsByClassName("smt-alert-closebtn");
		var i;
		
		for (i = 0; i < close.length; i++) {
		close[i].onclick = function(){
				var div = this.parentElement;
				div.style.opacity = "0";
				setTimeout(function(){ div.style.display = "none"; }, 600);
			}
		}

	});

})( jQuery );

var page = 1;

/**
 * Filter and show medias
 * 
 */
function smet_load_filtered_media(page) {
	let m_categories = jQuery('#smt-attachment-filters-media-categories').val();
	let m_type = jQuery('#media-attachment-type-filters').val();
	let from_date = jQuery('#media-attachment-date-filters-from').val(); 
	let to_date = jQuery('#media-attachment-date-filters-to').val();
	jQuery.ajax({
		url: smet_show_filtered_media.ajax_url,
		type: 'POST',
		data: {
			'media-categories': m_categories,
			'from-date': from_date,
			'to-date': to_date,
			'm-type': m_type,
			'smt-curr-page': page,
			'action': 'smet_show_filtered_media'
		},
		beforeSend: function() {
			smet_show_loader();
		},
		success: function(data) {
			jQuery(".smt-media-list").html(data);
			smet_hide_loader();
		},
		error: function(error) {
			console.log(error);
			smet_hide_loader();
		}
	});
}

/**
 * Nav buttons click
 * 
 */
function smet_nav_btn_click(elem) {
	page = jQuery(elem).attr('p');
	smet_load_filtered_media(page);
}

/**
 * Edit attachment meta data
 *
*/
function smet_edit_media(elem, attachment_id) {
	let alt_text = jQuery(elem).closest('.smt-file-data').find('#smt-attachment-details-alt-text').val();
	let title = jQuery(elem).closest('.smt-file-data').find('#smt-attachment-details-title').val();
	let caption = jQuery(elem).closest('.smt-file-data').find('#smt-attachment-details-caption').val();
	let description = jQuery(elem).closest('.smt-file-data').find('#smt-attachment-details-description').val();
	let media_categories = jQuery(elem).closest('.smt-file-data').find('#smt-attachment-details-media-categories').val();
	jQuery.ajax({
		url: smet_edit_metadata.ajax_url,
		type: 'POST',
		data: {
			'attachemnt-id': attachment_id,
			'alt-text': alt_text,
			'title': title,
			'caption': caption,
			'description': description,
			'media-categories': media_categories,
			'action': 'smet_edit_metadata'
		},
		beforeSend: function() {
			smet_show_loader();
		},
		success: function(data) {		
			if(data.success)
			{
				smet_show_success_pop_up(data.data.description);
				jQuery(".smt-modal").css("display", "none");		
			}
			else {
				smet_show_alert_pop_up(data.data.description);
			}
			smet_hide_loader();	
		}
	});
}

/**
 * Image click event
 *
*/
function smet_image_click(id, elem) {

	// Select for delete media case
	if(jQuery(elem).find('img').hasClass('smt-to-select')) {
		jQuery(elem).toggleClass('smt-selected-to-delete');
		var selectedIds = jQuery('.smt-selected-to-delete').map(function() {
			return jQuery(this).attr('data-id');
		}).get();
		jQuery('#smt-ids-to-bulk').val(selectedIds);
		// counter
		jQuery('#smt-selected-count span').html(selectedIds.length);
		
		
	}
	else {
		// Retrieve info case
		let attachemnt_id = id;
		jQuery.ajax({
			url: smet_retrieve_info.ajax_url,
			type: 'POST',
			data: {
				'attachemnt-id': attachemnt_id,
				'action': 'smet_retrieve_info'
			},
			beforeSend: function() {
				smet_show_loader();
			},
			success: function(data) {		
				jQuery("#smt-modal").css("display", "block");
				jQuery(".smt-modal-inner-content").html(data);
				jQuery('.smt-selectize-multiple-details').selectize({});
				smet_hide_loader();		
			},
			error: function(error) {
				console.log(error);
				smet_hide_loader();
			}
		});
	}
}

/**
 * Delete Media
 *
*/
function smet_delete_media_group() {
	var result = window.confirm('Are you sure?');
	if (result == false) {
		return;
	};
	let ids_to_delete = jQuery('#smt-ids-to-bulk').val();
	jQuery.ajax({
		url: smet_delete_media.ajax_url,
		type: 'POST',
		data: {
			'ids-to-delete': ids_to_delete,
			'action': 'smet_delete_media'
		},
		beforeSend: function() {
			smet_show_loader();
		},
		success: function(data) {	
			if(data.success)
			{
				smet_show_success_pop_up(data.data.description);
				jQuery("#smt-multiple-select-back-media-btn").click();
				smet_load_filtered_media(page);
				smet_hide_loader();	
			}
			else {
				smet_show_alert_pop_up(data.data.description);
				jQuery("#smt-multiple-select-back-media-btn").click();
				smet_load_filtered_media(page);
				smet_hide_loader();
			}	

		}
	});
	
}

/**
 * Show media category only modal (fired after adding new media)
 *
*/
function smet_add_only_media_cat(gallery_ids = {}) {
	jQuery("#smt-modal-media-cat").css("display", "block");
	let thumbnails = '';
	let ids = [];
	jQuery.each(gallery_ids, function( index, value ) {
		thumbnails += '<img src="'+value+'" style="padding: 5px;" />';
		ids.push(index);
	});
	jQuery("#smt-modal-media-cat .smt-modal-inner-content").html('<div class="smt-file-type-modal">'+thumbnails+'</div>');	
	jQuery("#smt-modal-media-cat .smt-id-value").html('<input type="hidden" value="'+ ids +'">');
	jQuery('.smt-selectize-multiple-add-first')[0].selectize.clear();
}

/**
 * Save attachement media categories only (click on save -> small modal after new media has been added)
 *
*/
function smet_save_media_categories_only(elem) {
	let media_ids = jQuery(elem).next('.smt-id-value').children('input').val();
	let media_categories = jQuery(elem).closest('.smt-modal-content').find('#smt-attachment-media-categories').val();

	jQuery.ajax({
		url: smet_save_cat_only.ajax_url,
		type: 'POST',
		data: {
			'media_ids': media_ids.split(','),
			'media-categories': media_categories,
			'action': 'smet_save_cat_only'
		},
		beforeSend: function() {
			smet_show_loader();
		},
		success: function(data) {
			if(data.success)
			{
				smet_show_success_pop_up(data.data.description);
				jQuery(".smt-modal").css("display", "none");
			}
			else {
				smet_show_alert_pop_up(data.data.description);
			}
			smet_hide_loader();
		}
	});
}

/**
 * Delete Media Category
 *
*/
function smet_delete_media_cat(elem, media_id) {
	var result = window.confirm('Are you sure?');
	if (result == false) {
		return;
	};
	jQuery.ajax({
		url: smet_delete_media_category.ajax_url,
		type: 'POST',
		data: {
			'media-category-id': media_id,
			'action': 'smet_delete_media_category'
		},
		beforeSend: function() {
			smet_show_loader();
		},
		success: function(data) {		
			if(data.success)
			{
				smet_show_success_pop_up(data.data.description);
				jQuery(elem).closest('tr').remove();
				jQuery(".smt-modal").css("display", "none");
			}
			else {
				smet_show_alert_pop_up(data.data.description);
			}
			smet_hide_loader();
		}
	});
}

/**
 * Show edit Media Category modal
 * 
*/
function smet_show_edit_media_cat_modal(elem, media_id) {
	let cat_id = jQuery(elem).val();
	jQuery('#smt-edit-cat-id').val(cat_id);
	jQuery('#smt-edit-cat-textfield').val('');
	jQuery("#smt-modal-edit-cat").css("display", "block");

}

/**
 * Bind loader to show only during ajax calls
 * 
*/
function smet_show_loader() {
	jQuery('.smt-plugin-context .smt-body').hide();
	jQuery('.smt-plugin-context .loader').show();  // show loading indicator
}

function smet_hide_loader() {
	jQuery('.smt-plugin-context .loader').hide();  // hide loading indicator
	jQuery('.smt-plugin-context .smt-body').show();
}

/**
 * Popups visibility
 * 
*/
function smet_show_success_pop_up(text) {
	jQuery('.smt-plugin-context .smt-alert.success .smt-success-popup-text').html(text);
	jQuery('.smt-plugin-context .smt-alert.success').show();
	setTimeout(function(){ jQuery('.smt-plugin-context .smt-alert.success').hide(); }, 2500);
}

function smet_show_alert_pop_up(text) {
	jQuery('.smt-plugin-context .smt-alert.alert .smt-alert-popup-text').html(text);
	jQuery('.smt-plugin-context .smt-alert.alert').show();
	setTimeout(function(){ jQuery('.smt-plugin-context .smt-alert').hide(); }, 2500);
}