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
    var ad_keywords = [];
<?php 
  if (get_the_ID() && false) :
    $keywords = get_the_terms(get_the_ID(), 'mj_primary_tag');
    $keywords[] = get_the_category();
    print '<!-- ';
    print_r($keywords);
    print ' -->';
  ?>
    ad_keywords = [<?php print join('+', 
      str_replace('+', '_', $keywords) //haha omg does this actually work?
    );?>];
<?php endif; ?>

		var adtech_code = function(placement, height) {
				var curDateTime = new Date(); 
				var offset = -(curDateTime.getTimezoneOffset()); 
				if (offset > 0) { offset = "+" + offset; }
				document.write(
						'<scr'+'ipt language="javascript1.1" src="http://adserver.adtechus.com/addyn/3.0/5443.1/0/0/'
						+ escape(height)+'/ADTECH;loc=100;target=_blank'
						+ ';alias=' + escape(placement)
						+ ';key=' + escape(window.ad_keywords)
						+ ';grp=' + escape(window.groupid)
						+ ';kvuri=' + escape(window.location.pathname)
						+ ';misc=' + curDateTime.getTime()
						+ ';aduho=' + offset + '"></scri'+'pt>'
				); 
		}
  </script>
</head>

<body <?php body_class(); ?>>
<?php get_template_part( 'template-parts/floating-navbar'); ?>
<?php dynamic_sidebar( 'page-top' ); ?>
<div id="page" class="site">

		<header id="masthead" class="site-header" role="banner">
			<?php get_template_part( 'template-parts/static-navbar'); ?>
			</div><!-- .site-header-main -->

		</header><!-- .site-header -->

    <?php dynamic_sidebar( 'ticker' ); ?>
