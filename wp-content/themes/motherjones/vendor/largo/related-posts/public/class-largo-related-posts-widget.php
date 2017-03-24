<?php
/*
 * List all of the terms in a custom taxonomy
 */
class largo_related_posts_widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname' 	=> 'largo-related-posts',
			'description' 	=> __('Lists posts related to the current post', 'largo')
		);
		parent::__construct( 'largo-related-posts-widget', __('Largo Related Posts', 'largo'), $widget_ops);
	}

	function widget( $args, $instance ) {
		global $post;
		// Preserve global $post
		$preserve = $post;
		extract( $args );

		// only useful on post pages
		if ( !is_single() ) return;

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Read Next', 'largo' ) : $instance['title'], $instance, $this->id_base);

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

 		$related = new Largo_Related( $instance['qty'] );
 		//get the related posts
 		$rel_posts = new WP_Query( array(
 			'post__in' => $related->ids(),
			'nopaging' => 1,
 			'posts_per_page' => $instance['qty'],
 			'ignore_sticky_posts' => 1 
 		) );
		if ( $rel_posts->have_posts() ) {
			
			$display_class = 'simple' == $instance['display'] ? 'simple' : 'full';	

			echo '<ul class="related ' . $display_class . '">';

	 		while ( $rel_posts->have_posts() ) {
		 		$rel_posts->the_post();
		 		echo '<li>';
					if ( 'simple' == $instance['display'] ) {
						?>
						<h4><a href="<?php the_permalink(); ?>" title="Read: <?php esc_attr( the_title('','', FALSE) ); ?>"><?php the_title(); ?></a></h4>
						<?php
					}
					else {
						echo '<a href="' . get_permalink() . '"/>' . get_the_post_thumbnail( get_the_ID(), 'thumbnail', array('class'=>'alignleft') ) . '</a>';
						?>
						<h4><a href="<?php the_permalink(); ?>" title="Read: <?php esc_attr( the_title('','', FALSE) ); ?>"><?php the_title(); ?></a></h4>
						<h5 class="byline">
							<span class="by-author"><?php $this->largo_byline( true, false ); ?></span>
						</h5>
						<?php // post excerpt/summary
						$this->largo_excerpt(get_the_ID(), 2, null, null, true);
					}
		 		echo '</li>';
	 		}

	 		echo "</ul>";
 		}
		echo $after_widget;
		// Restore global $post
		wp_reset_postdata();
		$post = $preserve;
	}

	/**
	 * Make a nicer-looking excerpt regardless of how an author has been using excerpts in the past
	 *
	 * @param $post object the post
	 * @param $sentence_count int the number of sentences to show
	 * @param $use_more bool append read more link to end of output
	 * @param $more_link string the text of the read more link
	 * @param $echo bool echo the output or return it (default: echo)
	 * @param $strip_tags|$strip_shortcodes bool
	 * @uses largo_trim_sentences
	 * @package largo
	 * @since 0.3
	 */
	function largo_excerpt( $the_post=null, $sentence_count = 5, $use_more = null, $more_link = null, $echo = true, $strip_tags = true, $strip_shortcodes = true ) {
		if (!empty($use_more))
			_deprecated_argument(__FUNCTION__, '0.5.1', 'Parameter $use_more is deprecated. Please use null as the argument.');
		if (!empty($more_link))
			_deprecated_argument(__FUNCTION__, '0.5.1', 'Parameter $more_link is deprecated. Please use null as the argument.');

		$the_post = get_post($the_post); // Normalize it into a post object

		if (!empty($the_post->post_excerpt)) {
			// if a post has a custom excerpt set, we'll use that
			$content = apply_filters('get_the_excerpt', $the_post->post_excerpt);
		} else if (is_home() && preg_match('/<!--more(.*?)?-->/', $the_post->post_content, $matches) > 0) {
			// if we're on the homepage and the post has a more tag, use that
			$parts = explode($matches[0], $the_post->post_content, 2);
			$content = $parts[0];
		} else {
			// otherwise we'll just do our best and make the prettiest excerpt we can muster
			$content = $this->largo_trim_sentences($the_post->post_content, $sentence_count);
		}

		// optionally strip shortcodes and html
		$output = '';
		if ( $strip_tags && $strip_shortcodes )
			$output .= strip_tags( strip_shortcodes ( $content ) );
		else if ( $strip_tags )
			$output .= strip_tags( $content );
		else if ( $strip_shortcodes )
			$output .= strip_shortcodes( $content );
		else
			$output .= $content;

		$output = apply_filters('the_content', $output);

		if ( $echo )
			echo $output;

		return $output;
	}

	/**
	 * Attempt to trim input at sentence breaks
	 *
	 * @param $input string
	 * @param $sentences number of sentences to trim to
	 * @param $echo echo the string or return it (default: return)
	 * @return $output trimmed string
	 *
	 * @since 0.3
	 */
	function largo_trim_sentences( $input, $sentences, $echo = false ) {
		$re = '/# Split sentences on whitespace between them.
			(?<=                # Begin positive lookbehind.
				[.!?]           	# Either an end of sentence punct,
				| [.!?][\'"]    	# or end of sentence punct and quote.
			)                   # End positive lookbehind.
			(?<!                # Begin negative lookbehind.
				Mr\.            	# Skip either "Mr."
			    | Mrs\.             # or "Mrs.",
			    | Ms\.              # or "Ms.",
			    | Jr\.              # or "Jr.",
			    | Dr\.              # or "Dr.",
			    | Prof\.            # or "Prof.",
			    | Sr\.              # or "Sr.",
			    | Rep\.             # or "Rep.",
			    | Sen\.             # or "Sen.",
			    | Gov\.             # or "Gov.",
			    | Pres\.            # or "Pres.",
			    | U\.S\.            # or "U.S.",
			    | Rev\.            	# or "Rev.",
			    | Gen\.        		# or "Gen.",
			    | Capt\.            # or "Capt.",
			    | Lt\.            	# or "Lt.",
			    | Cpl\.            	# or "Cpl.",
			    | Inc\.            	# or "Inc.",
			    | \s[A-Z]\.         # or initials ex: "George W. Bush",
			    | [A-Z]\.[A-Z]\.    # or random state abbreviations ex: "O.H.",
			)                   # End negative lookbehind.
			\s+                 # Split on whitespace between sentences.
			/ix';

		$strings = preg_split( $re, strip_tags( strip_shortcodes( $input ) ), -1, PREG_SPLIT_NO_EMPTY);

		$output = '';

		for ( $i = 0; $i < $sentences && $i < count($strings); $i++ ) {
			if ( $strings[$i] != '' )
				$output .= $strings[$i] . ' ';
		}

		if ( $echo )
			echo $output;

		return $output;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['qty'] = (int) $new_instance['qty'];
		$instance['display'] = sanitize_text_field( $new_instance['display'] );
		$instance['show_byline'] = (int) $new_instance['show_byline'];
		$instance['thumbnail_location'] = sanitize_key( $new_instance['thumbnail_location'] );
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Read Next', 'qty' => 1, 'display' => 'simple', 'show_byline' => 0, 'thumbnail_location' => 'before') );
		$title = esc_attr( $instance['title'] );
		$qty = $instance['qty'];
		$display = $instance['display'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title', 'largo' ); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('qty'); ?>"><?php _e('Number of Posts to Display', 'largo'); ?>:</label>
			<select name="<?php echo $this->get_field_name('qty'); ?>" id="<?php echo $this->get_field_id('qty'); ?>">
			<?php
			for ($i = 1; $i < 6; $i++) {
				echo '<option value="', $i, '"', selected($qty, $i, FALSE), '>', $i, '</option>';
			} ?>
			</select>
			<div class="description"><?php _e( "It's best to keep this at just one.", 'largo' ); ?></div>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('display'); ?>"><?php _e('Display Type', 'largo'); ?>:</label>
			<select name="<?php echo $this->get_field_name('display'); ?>" id="<?php echo $this->get_field_id('display'); ?>">
				<option value="simple"<?php if ( 'simple' == $display ) echo ' selected="selected"'; ?>>Simple</option>
				<option value="full"<?php if ( 'full' == $display ) echo ' selected="selected"'; ?>>Full</option>
			</select>
		</p>

		<p><input id="<?php echo $this->get_field_id('show_byline'); ?>" name="<?php echo $this->get_field_name('show_byline'); ?>" type="checkbox" value="1" <?php checked( $instance['show_byline'], 1);?> />
			<label for="<?php echo $this->get_field_id('show_byline'); ?>"><?php _e( 'Show date with each post', 'largo' ); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('thumbnail_location'); ?>"><?php _e('Thumbnail position', 'largo'); ?>:</label>
			<select name="<?php echo $this->get_field_name('thumbnail_location'); ?>" id="<?php echo $this->get_field_id('thumbnail_location'); ?>">
			<?php
			$choices = array( 'before' => __( 'Before Headline', 'largo' ), 'after' => __( 'After Headline', 'largo' ) );
			foreach( $choices as $i => $display ) {
				echo '<option value="', $i, '"', selected($instance['thumbnail_location'], $i, false), '>', $display, '</option>';
			} ?>
			</select>
		</p>

	<?php
	}

	/**
	 * Outputs custom byline and link (if set), otherwise outputs author link and post date
	 *
	 * @param Boolean $echo Echo the string or return it (default: echo)
	 * @param Boolean $exclude_date Whether to exclude the date from byline (default: false)
	 * @param WP_Post|Integer $post The post object or ID to get the byline for. Defaults to current post.
	 * @return String Byline as formatted html
	 * @since 0.3
	 */
	function largo_byline( $echo = true, $exclude_date = false, $post = null ) {

		// Get the post ID
		if (!empty($post)) {
			if (is_object($post))
				$post_id = $post->ID;
			else if (is_numeric($post))
				$post_id = $post;
		} else {
			$post_id = get_the_ID();
		}

		// Set us up the options
		// This is an array of things to allow us to easily add options in the future
		$options = array(
			'post_id' => $post_id,
			'values' => get_post_custom( $post_id ),
			'exclude_date' => $exclude_date,
		);

		if ( isset( $options['values']['largo_byline_text'] ) && !empty( $options['values']['largo_byline_text'] ) ) {
			// Temporary placeholder for largo custom byline option
			$byline = new Largo_Custom_Byline( $options );
		} else if ( function_exists( 'get_coauthors' ) ) {
			// If Co-Authors Plus is enabled and there is not a custom byline
			$byline = new Largo_CoAuthors_Byline( $options );
		} else {
			// no custom byline, no coauthors: let's do the default
			$byline = new Largo_Byline( $options );
		}

		/**
		 * Filter the largo_byline output text to allow adding items at the beginning or the end of the text.
		 *
		 * @since 0.5.4
		 * @param string $partial The HTML of the output of largo_byline(), before the edit link is added.
		 * @link https://github.com/INN/Largo/issues/1070
		 */
		$byline = apply_filters( 'largo_byline', $byline );

		if ( $echo ) {
			echo $byline;
		}
		return $byline;
	}

}
