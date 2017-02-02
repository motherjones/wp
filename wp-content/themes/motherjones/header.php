<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package MotherJones
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>
  <link rel="stylesheet" href="/wp-content/themes/motherjones/css/font-awesome-4.6.3/css/font-awesome.min.css">
	<?php wp_head(); ?>
  <?php
    GLOBAL $ad_group_id;
    $ad_group_id = rand(0, 100000000)
  ?>
  <script>
      //let's make these ad code variables available just in case
      //we have to place ads with js somewhere down the line;
    var ad_group_id = <?php print $ad_group_id;?>;
    var ad_keywords = '';
    var is_post = false;
    var is_fullwidth = false;

<?php 
  if (get_the_ID()) :
    $keyword_term_objs = get_the_terms(get_the_ID(), 'mj_primary_tag');
    $keyword_term_objs[] = get_the_category()[0];
    $keyword_terms = [];
    $is_fullwidth = get_post_type() === 'mj_full_width';
    foreach ($keyword_term_objs as $obj) {
      $keyword_terms[] = str_replace('+', '_', $obj->slug);
    }
  ?>
    ad_keywords = '<?php print join('+', $keyword_terms);?>';
    is_post = true;
    <?php if ($is_fullwidth) { print 'is_fullwidth = true;'; } ?>
<?php endif; ?>
</script>
<script type="text/javascript" src="/wp-content/themes/motherjones/js/jquery-3.1.0.min.js"></script>
<script type="text/javascript" src="/wp-content/themes/motherjones/js/ad_code.js"></script>
</head>

<body <?php body_class(); ?>>
<?php
       $blogtypes = get_terms( array(
         'taxonomy' => 'mj_media_type',
         'hide_empty' => false,
       ) );
       foreach ($blogtypes as $blogtype) {
				print_r( '^' . $blogtype->slug . '/([^/]*)$' );
			}
?>
<?php get_template_part( 'template-parts/floating-navbar'); ?>
<?php dynamic_sidebar( 'page-top' ); ?>
<div id="page" class="site">

		<header id="masthead" class="site-header" role="banner">
			<?php get_template_part( 'template-parts/static-navbar'); ?>

		</header><!-- .site-header -->

    <?php dynamic_sidebar( 'ticker' ); ?>
