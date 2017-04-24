<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>
	<script>
	var ad_group_id = Math.floor(Math.random() * 100000000);
	var ad_keywords = '';
	var is_post = false;
	var is_fullwidth = false;

	<?php
	$keyword_terms = [];
	$domain = $_SERVER['SERVER_NAME'];
	$keyword_terms[] = substr( $domain, 0, strpos( $domain, '.' ) );
	if ( is_singular() ) {
		$keyword_term_objs = get_the_tags( get_the_ID() );
		$keyword_term_objs[] = get_the_category( get_the_ID() )[0];
		foreach ( $keyword_term_objs as $obj ) {
			$keyword_terms[] = str_replace( '+', '_', $obj->slug );
		}
		echo "is_post = true;\n";
		if ( mj_is_content_type( 'full_width_article', get_the_ID() ) ) {
			echo "is_fullwidth = true;\n";
		}
	} elseif ( is_archive() ) {
		$keyword_terms[] = get_queried_object()->slug;
	}
	echo "ad_keywords = '" . join( '+', $keyword_terms ) . "';";
	?>
	</script>

	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/apple-touch-icon-57x57.png" />
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/apple-touch-icon-114x114.png" />
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/apple-touch-icon-144x144.png" />
	<link rel="apple-touch-icon-precomposed" sizes="60x60" href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/apple-touch-icon-60x60.png" />
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/apple-touch-icon-120x120.png" />
	<link rel="apple-touch-icon-precomposed" sizes="76x76" href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/apple-touch-icon-76x76.png" />
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/apple-touch-icon-152x152.png" />
	<link rel="icon" type="image/png" href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/favicon-196x196.png" sizes="196x196" />
	<link rel="icon" type="image/png" href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/png" href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/favicon-32x32.png" sizes="32x32" />
	<link rel="icon" type="image/png" href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/favicon-128.png" sizes="128x128" />
	<meta name="application-name" content="Mother Jones Magazine"/>
	<meta name="msapplication-TileColor" content="#FFFFFF" />
	<meta name="msapplication-TileImage" content="<?php echo esc_url( get_template_directory_uri() ); ?>/img/mstile-144x144.png" />
	<meta name="msapplication-square70x70logo" content="<?php echo esc_url( get_template_directory_uri() ); ?>/img/mstile-70x70.png" />
	<meta name="msapplication-square150x150logo" content="<?php echo esc_url( get_template_directory_uri() ); ?>/img/mstile-150x150.png" />
	<meta name="msapplication-wide310x150logo" content="<?php echo esc_url( get_template_directory_uri() ); ?>/img/mstile-310x150.png" />
	<meta name="msapplication-square310x310logo" content="<?php echo esc_url( get_template_directory_uri() ); ?>/img/mstile-310x310.png" />

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<a href="#main" class="visuallyhidden">Skip to main content</a>
<?php get_template_part( 'template-parts/floating-navbar' ); ?>
<?php dynamic_sidebar( 'page-top' ); ?>
<div id="page" class="grid">
	<?php
	if ( ! is_home() ) {
		the_widget(
			'mj_ad_unit_widget',
			array(
				'placement' => 'TopROS970x250',
				'height' => 2473,
				'docwrite' => 1,
				'desktop' => 1,
			),
			array(
				'before_widget' => '<div id="TopROS970x250" class="ad-unit grid__col-12" >',
				'after_widget' => '</div>',
			)
		);
	}
	?>

	<header id="masthead" class="site-header grid__col-12 grid__col--bleed" role="banner">
		<?php get_template_part( 'template-parts/static-navbar' ); ?>
	</header><!-- .site-header -->

	<?php
	if ( ! isset( $mj['meta']['mj_hide_ads'] ) ) {
		echo '<div id="ticker" class="grid__col-12 grid__col--bleed">';
		dynamic_sidebar( 'ticker' );
		echo '</div>';
	}
	?>
