jQuery(document).ready(function($) {
	// Register a click event handler on the remove links
	$('.mfi-reloaded-image-picker-remove').click(function(event) {
		event.preventDefault();

		var $remove = $(this),
			$container = $remove.parents('.mfi-reloaded-image-picker'),
			$preview = $remove.siblings('.mfi-reloaded-image-picker-preview'),
			$set = $remove.siblings('.mfi-reloaded-image-picker-set'),
			_name = $container.data('mfi-reloaded-name');

		// Initiate an AJAX request to remove the image id for this image picker
		$.post(
			ajaxurl,
			{
				action: 'mfi_reloaded_set_image_id',
				image_id: 0,
				name: _name,
				post_id: $('#post_ID').val()
			},
			function(data, status) { }
		);

		// Hide the link that allows a user to remove the image
		$remove.hide();

		// Remove the preview thumbnail because it is no longer valid
		$preview.empty();

		// Show the link that allows a user to set the image
		$set.show();
	});

	// Register a click event handler in order to show the media picker
	$('.mfi-reloaded-image-picker-set').click(function(event) {
		event.preventDefault();

		var $set = $(this),
			$container = $set.parents('.mfi-reloaded-image-picker'),
			$preview = $set.siblings('.mfi-reloaded-image-picker-preview'),
			$remove = $set.siblings('.mfi-reloaded-image-picker-remove'),
			_name = $container.data('mfi-reloaded-name'),
			_select = $container.data('mfi-reloaded-select'),
			_title = $container.data('mfi-reloaded-title');

		// Set up the media picker frame
		var mfi_reloaded_frame = wp.media({
			// Open the media picker in select mode only
			frame: 'select',

			// Only allow a single image to be chosen
			multiple: false,

			// Set the popup title from the HTML markup we output for the active picker
			title: _title,

			// Only allow the user to choose form images
			library: { type: 'image' },

			button: {
				// Set the button text from the HTML markup we output for the active picker
				text:  _select
			}
		});

		mfi_reloaded_frame.on('select', function(){
			var media_attachment = mfi_reloaded_frame.state().get('selection').first().toJSON();

			// Initiate an AJAX request to set the image id for this image picker
			$.post(
				ajaxurl,
				{
					action: 'mfi_reloaded_set_image_id',
					image_id: media_attachment.id,
					name: _name,
					post_id: $('#post_ID').val()
				},
				function(data, status) { }
			);

			// Add the image to the preview container
			$preview.append($('<img />').attr('src', media_attachment.sizes.full.url).attr('alt', media_attachment.title));

			// Show the remove link
			$remove.show();

			// Hide the set link
			$set.hide();
		});

		mfi_reloaded_frame.open();
	});

	$('.mfi-reloaded-image-picker').each(function(index, element) {
		var $container = $(element),
			$preview = $container.children('.mfi-reloaded-image-picker-preview'),
			$remove = $container.children('.mfi-reloaded-image-picker-remove'),
			$set = $container.children('.mfi-reloaded-image-picker-set');


		if(0 === $preview.children().size()) {
			$remove.hide();
		} else {
			$set.hide();
		}
	});
});
