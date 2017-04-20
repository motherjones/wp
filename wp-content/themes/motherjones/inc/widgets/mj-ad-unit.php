<?php
/**
 * Generic Ad Unit
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */
class mj_ad_unit_widget extends WP_Widget {

	function __construct() {
		/* Widget settings. */
		$widget_ops = array(
			'classname' 	=> 'mj-ad-unit',
			'description' 	=> __( 'Display an ad unit with the required parameters.', 'mj' ),
		);

		/* Create the widget. */
		parent::__construct( 'mj-ad-unit-widget', __( 'Ad Unit', 'mj' ), $widget_ops );
	}

	/**
	 * Render the widget output
	 */
	function widget( $args, $instance ) {
		global $mj;
		if ( isset( $mj['meta']['mj_hide_ads'] ) ) {
			return;
		}
		extract( $args );
		echo $before_widget;
		?>
		<script>
			ad_code({
				<?php
				if ( isset( $instance['placement'] ) ) {
					echo "placement: '" . esc_js( $instance['placement'] ) . "',\n";
				}
				if ( isset( $instance['height'] ) ) {
					echo 'height: ' . esc_js( $instance['height'] ) . ",\n";
				}
				// Not optional, set to false if mobile.
				if ( $instance['desktop'] ) {
					echo "desktop: true,\n";
				} else {
					echo "desktop: false,\n";
				}
				// These are optional.
				if ( isset( $instance['docwrite'] ) && $instance['docwrite'] ) {
					echo "docwrite: true,\n";
				}
				if ( isset( $instance['yieldmo'] ) && $instance['yieldmo'] ) {
					echo "yieldmo: true,\n";
				}
				?>
			});
		</script>

		<?php
		echo $after_widget;
	}

	/**
	 * Widget update function: sanitizes title.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['placement'] = sanitize_text_field( $new_instance['placement'] );
		$instance['height'] = intval( $new_instance['height'] );
		$instance['docwrite'] = $new_instance['docwrite'];
		$instance['desktop'] = $new_instance['desktop'];
		$instance['yieldmo'] = $new_instance['yieldmo'];
		return $instance;
	}

	/**
	 * No options for this widget.
	 */
	function form( $instance ) {
		$defaults = array(
			'placement' => '',
			'height' => '',
			'docwrite' => '',
			'desktop' => '',
			'yieldmo' => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'placement' ); ?>"><?php _e( 'Placement (Ad Code ID)', 'mj' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'placement' ); ?>" name="<?php echo $this->get_field_name( 'placement' ); ?>" value="<?php echo $instance['placement']; ?>" style="width:90%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height', 'mj' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" style="width:90%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'docwrite' ); ?>"><?php _e( 'Docwrite or iframe?', 'mj' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'docwrite' ); ?>" name="<?php echo $this->get_field_name( 'docwrite' ); ?>" class="widefat" style="width:90%;">
				<option <?php selected( $instance['docwrite'], 1 ); ?> value="1"><?php _e( 'docwrite', 'mj' ); ?></option>
				<option <?php selected( $instance['docwrite'], 0 ); ?> value="0"><?php _e( 'iframe', 'mj' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'desktop' ); ?>"><?php _e( 'Desktop or mobile?', 'mj' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'desktop' ); ?>" name="<?php echo $this->get_field_name( 'desktop' ); ?>" class="widefat" style="width:90%;">
				<option <?php selected( $instance['desktop'], 1 ); ?> value="1"><?php _e( 'desktop', 'mj' ); ?></option>
				<option <?php selected( $instance['desktop'], 0 ); ?> value="0"><?php _e( 'mobile', 'mj' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'yieldmo' ); ?>"><?php _e( 'Yieldmo or adtech?', 'mj' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'yieldmo' ); ?>" name="<?php echo $this->get_field_name( 'yieldmo' ); ?>" class="widefat" style="width:90%;">
				<option <?php selected( $instance['yieldmo'], 1 ); ?> value="1"><?php _e( 'yieldmo', 'mj' ); ?></option>
				<option <?php selected( $instance['yieldmo'], 0 ); ?> value="0"><?php _e( 'adtech', 'mj' ); ?></option>
			</select>
		</p>

	<?php
	}
}
