<?php
/**
 * Floating Ad Widget
 * This generates the markup for the floating ad on single articles.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */
class mj_floating_ad_widget extends WP_Widget {

	function __construct() {
		/* Widget settings. */
		$widget_ops = array(
			'classname' 	=> 'mj-floating-ad',
			'description' 	=> __( 'Display the floating ad next to content on single posts.', 'mj' ),
		);

		/* Create the widget. */
		parent::__construct( 'mj-floating-ad-widget', __( 'Floating Ad', 'mj' ), $widget_ops );
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
		<div class="advertise-top"></div>
			<div id="criteoscroller">
				<script type="text/javascript" src="http://aka-cdn-ns.adtechus.com/dt/common/DAC.js"></script>
				<div id="4170840">
					<noscript>
					<a href="http://adserver.adtechus.com/adlink|3.0|5443.1|4170840|0|529|ADTECH;loc=300;key=key1+key2+key3+key4;alias=" target="_blank">
						<img src="http://adserver.adtechus.com/adserv|3.0|5443.1|4170840|0|529|ADTECH;loc=300;key=key1+key2+key3+key4;alias=" border="0" width="300" height="600">
					</a>
					</noscript>
				</div>
			</div>
			<script type="text/javascript">
				jQuery(window).load(function() {
					if (typeof MJ_HideRightColAds === 'undefined' &&   jQuery('#page-closure').offset().top > 2800 ) {

						ADTECH.config.page = { protocol: 'http', server: 'adserver.adtechus.com', network: '5443.1', pageid: 634599, params: { loc: '100' }};
						ADTECH.config.placements[4170840] = { sizeid: 529, params: { alias: '', target: '_blank' }};
						ADTECH.loadAd(4170840);
						// Set a function to load an ad every 55,000 miliseconds (55 seconds)
						setInterval(function(){ ADTECH.loadAd(4170840); }, 30000);

						var criteo_scrollingAd = jQuery('#criteoscroller');var criteo_adTop = criteo_scrollingAd.offset().top;
						function fixDiv() {
								var criteo_stoppingHeight = jQuery('#page-closure').offset().top;
								if (jQuery(window).scrollTop() >= (criteo_stoppingHeight - 650))
										criteo_scrollingAd.css({
												'position': 'relative',
												'top': (criteo_stoppingHeight - criteo_adTop - 650) + 'px'
										});
								else if (jQuery(window).scrollTop() >= (criteo_adTop - 50))
										criteo_scrollingAd.css({
												'position': 'fixed',
												'top': '50px'
										});
								else
										criteo_scrollingAd.css({
												'position': 'static',
												'top': 'auto'
										});
						}
						jQuery(window).scroll(fixDiv);
						fixDiv();
					}
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
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}

	/**
	 * No options for this widget.
	 */
	function form( $instance ) {
		return true;
	}
}
