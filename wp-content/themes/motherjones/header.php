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
	if ( is_singular() ) {
	    $keyword_term_objs = get_the_tags( get_the_ID() );
	    $keyword_term_objs[] = get_the_category( get_the_ID() )[0];
	    $keyword_terms = [];
	    $is_fullwidth = get_post_type() === 'mj_full_width';
	    foreach ( $keyword_term_objs as $obj ) {
			$keyword_terms[] = str_replace( '+', '_', $obj->slug );
	    }
		?>
	    ad_keywords = '<?php print join( '+', $keyword_terms );?>';
	    is_post = true;
	    <?php if ( $is_fullwidth ) { print 'is_fullwidth = true;'; } ?>
	<?php
	} elseif ( is_archive() ) { ?>
	  ad_keywords = '<?php print get_queried_object()->slug; ?>';
	<?php
	} ?>
	</script>

	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="<?php print get_template_directory_uri(); ?>/img/apple-touch-icon-57x57.png" />
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php print get_template_directory_uri(); ?>/img/apple-touch-icon-114x114.png" />
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php print get_template_directory_uri(); ?>/img/apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php print get_template_directory_uri(); ?>/img/apple-touch-icon-144x144.png" />
	<link rel="apple-touch-icon-precomposed" sizes="60x60" href="<?php print get_template_directory_uri(); ?>/img/apple-touch-icon-60x60.png" />
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?php print get_template_directory_uri(); ?>/img/apple-touch-icon-120x120.png" />
	<link rel="apple-touch-icon-precomposed" sizes="76x76" href="<?php print get_template_directory_uri(); ?>/img/apple-touch-icon-76x76.png" />
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php print get_template_directory_uri(); ?>/img/apple-touch-icon-152x152.png" />
	<link rel="icon" type="image/png" href="<?php print get_template_directory_uri(); ?>/img/favicon-196x196.png" sizes="196x196" />
	<link rel="icon" type="image/png" href="<?php print get_template_directory_uri(); ?>/img/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/png" href="<?php print get_template_directory_uri(); ?>/img/favicon-32x32.png" sizes="32x32" />
	<link rel="icon" type="image/png" href="<?php print get_template_directory_uri(); ?>/img/favicon-128.png" sizes="128x128" />
	<meta name="application-name" content="Mother Jones Magazine"/>
	<meta name="msapplication-TileColor" content="#FFFFFF" />
	<meta name="msapplication-TileImage" content="<?php print get_template_directory_uri(); ?>/img/mstile-144x144.png" />
	<meta name="msapplication-square70x70logo" content="<?php print get_template_directory_uri(); ?>/img/mstile-70x70.png" />
	<meta name="msapplication-square150x150logo" content="<?php print get_template_directory_uri(); ?>/img/mstile-150x150.png" />
	<meta name="msapplication-wide310x150logo" content="<?php print get_template_directory_uri(); ?>/img/mstile-310x150.png" />
	<meta name="msapplication-square310x310logo" content="<?php print get_template_directory_uri(); ?>/img/mstile-310x310.png" />

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php get_template_part( 'template-parts/floating-navbar' ); ?>
<?php dynamic_sidebar( 'page-top' ); ?>
<div id="page" class="grid">
	<?php if ( get_the_ID() ) : ?>
		<div id="TopROS970x250" class="ad-unit grid__col-12" >
			<script>
	    	<!--
	      if ( typeof MJ_HideTopROS970x250 === 'undefined' ) {
	        ad_code({
	          desktop: true,
	          placement: 'TopROS970x250',
	          height: 2473,
	          doc_write: true,
	        });
	      }
	      //-->
	    </script>
		</div>
	<?php endif; ?>

	<header id="masthead" class="site-header grid__col-12 grid__col--bleed" role="banner">
		<?php get_template_part( 'template-parts/static-navbar' ); ?>
	</header><!-- .site-header -->

	<div class="grid__col-12 grid__col--bleed">
	<?php dynamic_sidebar( 'ticker' ); ?>
	</div>
