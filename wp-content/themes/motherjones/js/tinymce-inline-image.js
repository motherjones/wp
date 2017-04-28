/* global tinymce */
tinymce.PluginManager.add('wpgallery', function( editor ) {

	function image_align(name, node) {
		console.log('ehre');
		var wrap = editor.dom.getParent( node, 'figure.inline-image' );
		if ( wrap ) {
			node = wrap;
		}
		var class_attr = name;
		if ( node.nodeName === 'FIGURE' ) {
			class_attr += ' inline-image';
		}
			console.log(node);
		editor.dom.setAttrib(node, 'class', class_attr);
	}

	function removeImage( node ) {
		var wrap = editor.dom.getParent( node, 'figure.inline-image' );

		if ( ! wrap && node.nodeName === 'IMG' ) {
			wrap = editor.dom.getParent( node, 'a' );
		}

		if ( wrap ) {
			if ( wrap.nextSibling ) {
				editor.selection.select( wrap.nextSibling );
			} else if ( wrap.previousSibling ) {
				editor.selection.select( wrap.previousSibling );
			} else {
				editor.selection.select( wrap.parentNode );
			}

			editor.selection.collapse( true );
			editor.dom.remove( wrap );
		} else {
			editor.dom.remove( node );
		}

		editor.nodeChanged();
		editor.undoManager.add();
	}

	function isPlaceholder( node ) {
		return !! ( editor.dom.getAttrib( node, 'data-mce-placeholder' ) || editor.dom.getAttrib( node, 'data-mce-object' ) );
	}

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
		return '</span><span class="media-credit">' + shortcode.content;
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
		img = img ? img + '</img>' : '';

		var $credit = figure.find('span.media-credit'); 
		var credit = $credit.html() 
			? '[credit]' + $credit.html() + '[/credit]' 
			: '[credit][/credit]';
		var $caption = figure.find('span.media-caption'); 
		var caption = $caption.html() 
			? '[caption]' + $caption.html() + credit + '[/caption]' 
			: '[caption]' + credit + '[/caption]';

		return '[inline_image ' + id + ' align="' + align + '" width="' + width
			+ '"]' + img + caption + '[/inline_image]';
	}

	function editMedia( node ) {

		if ( node.nodeName !== 'IMG' ) {
			return;
		}

		var image = dom.select( 'img[wp-media-selected]' )[0];
	}


	editor.on( 'BeforeSetContent', function( event ) {
		event.content = replaceInlineImageShortcodes( event.content );
	});

	editor.on( 'PostProcess', function( event ) {
		if ( event.get ) {
			event.content = restoreInlineImageShortcodes( event.content );
		}
	});

	editor.on( 'ResolveName', function( event ) {
		var dom = editor.dom,
		node = event.target;

		if ( node.nodeName === 'FIGURE' ) {
			if ( dom.getAttrib( node, 'class' ).match('align') ) {
				event.name = 'Image Container';
			}
		}
		if ( node.nodeName === 'FIGCAPTION' ) {
			if ( dom.hasClass( node, 'wp-caption-text' ) ) {
				event.name = 'Image Data';
			}
		}
		if ( node.nodeName === 'SPAN' && dom.hasClass( node, 'media-caption' ) ) {
			event.name = 'Image Caption';
		}
		if ( node.nodeName === 'SPAN' && dom.hasClass( node, 'media-credit' ) ) {
			event.name = 'Image Credit';
		}
	});
	editor.on( 'cut', function() {
		removeToolbar();
	});


	editor.addButton( 'mj_img_remove', {
		tooltip: 'Remove',
		icon: 'dashicon dashicons-no',
		onclick: function() {
			removeImage( editor.selection.getNode() );
		}
	} );

	tinymce.each( {
		alignleft: 'Align left',
		aligncenter: 'Align center',
		alignright: 'Align right',
		alignnone: 'No alignment'
	}, function( tooltip, name ) {
		var direction = name.slice( 5 );

		editor.addButton( 'mj_img_' + name, {
			tooltip: tooltip,
			icon: 'dashicon dashicons-align-' + direction,
			onclick: function() {
				console.log('ehre');
				image_align(name, editor.selection.getNode());
			},
			onPostRender: function() {
				var self = this;

				editor.on( 'NodeChange', function( event ) {
					var node;


					node = editor.dom.getParent( event.element, '.inline-image' ) || event.element;

					// Don't bother.
					if ( node.nodeName !== 'FIGURE' ) {
						return;
					}

					if ( 'alignnone' === name ) {
						self.active( ! /\balign(left|center|right)\b/.test( node.className ) );
					} else {
						self.active( editor.dom.hasClass( node, name ) );
					}
				} );
			}
		} );
	} );

	editor.once( 'preinit', function() {
		if ( editor.wp && editor.wp._createToolbar ) {
			toolbar = editor.wp._createToolbar( [
				'mj_img_alignleft',
				'mj_img_aligncenter',
				'mj_img_alignright',
				'mj_img_alignnone',
				'mj_img_remove'
			] );
		}
	} );

	editor.on( 'wptoolbar', function( event ) {
		console.log(event.element)
		var wrap = editor.dom.getParent( event.element, 'figure.inline-image' );
		console.log(wrap);
		if ( 	
			(  	wrap
				|| (event.element.nodeName === 'FIGURE' && editor.dom.hasClass( event.element.nodeName, 'inline-image' ) )
			)
			&& ! isPlaceholder( event.element ) 
		) {
			event.toolbar = toolbar;
		}
	} );

});
