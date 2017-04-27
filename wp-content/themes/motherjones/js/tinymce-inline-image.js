/* global tinymce */
tinymce.PluginManager.add('wpgallery', function( editor ) {

	function replaceInlineImageShortcodes( content ) {
		return wp.shortcode.replace( 'inline_image', content, inline_image_shortcode );
	}

	function inline_image_shortcode( shortcode ) {
		var content = wp.shortcode.replace('caption', shortcode.content, caption_shortcode);
		var id = shortcode.attrs['named']['id'] ? 'id="' + shortcode.attrs['named']['id'] + '"' : '';
		return '<figure ' + id + ' class="inline-image align' + 
				shortcode.attrs['named']['align'] 
				+ '" style="max-width: ' +  shortcode.attrs['named']['width'] + 'px;">'
				+ content
			+ '</figure>';
	}

	function caption_shortcode( shortcode ) {
		var content = wp.shortcode.replace('credit', shortcode.content, credit_shortcode);
		return '<figcaption class="wp-caption-text"><span class="media-caption">'
		+ content + '</span></figcaption>';
	}
	function credit_shortcode( shortcode ) {
		return '</span> <span class="media-credit">' + shortcode.content;
	}

	function restoreInlineImageShortcodes( content ) {
		var $content = jQuery('<div>' + content + '</div>');
		$content.find('figure.inline-image').replaceWith(inline_image_to_shortcode);
		return $content.html();

	}

	function inline_image_to_shortcode() {
		var figure = jQuery(this);
		var figureElem = jQuery(figure[0]);

		var id = figure.attr('id') ? 'id="' + figure.attr('id') + '"' : '';
		var align = /align([a-zA-Z]+)/.exec(figure.attr('class'))[1];
		var width = /([0-9]+)/.exec(figure.attr('style'))[1];
		var img = figure.find('img').prop('outerHTML');

		var $credit = figure.find('span.media-credit'); 
		var credit = $credit.html() 
			? '[credit]' + $credit.html() + '[/credit]' 
			: '';
		var $caption = figure.find('span.media-caption'); 
		var caption = $caption.html() 
			? '[caption]' + $caption.html() + credit + '[/caption]' 
			: credit;

		return '[inline_image ' + id + ' align="' + align + '" width="' + width
			+ '"]' + img + caption + '[/inline_image]';
	}

	editor.on( 'BeforeSetContent', function( event ) {
		event.content = replaceInlineImageShortcodes( event.content );
	});

	editor.on( 'PostProcess', function( event ) {
		if ( event.get ) {
			event.content = restoreInlineImageShortcodes( event.content );
		}
	});
});
