<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main homepage" role="main">
    
      <div id="homepage-top" class="group">
        <?php $top_stories = z_get_zone_query('top_stories', array(
          'posts_per_page' => 10,
        ) ); ?>

        <?php $top_stories->the_post(); ?>
        <?php get_template_part( 'template-parts/homepage-top-story' ); ?>

        <ul id="homepage-top-story-side">
          <?php for ($i = 0; $i < 3; $i++): $top_stories->the_post(); ?>
            <?php get_template_part( 'template-parts/homepage-top-story-side' ); ?>
          <?php endfor;?>
        </ul>
      </div>

      <p> place an ad here </p>

      <div id="homepage-more-top-stories" class="group">
        <div id="homepage-more-top-stories-main">
          <h2 class="promo">More Top Stories</h2>
          <ul id="homepage-more-top-stories">
            <?php for ($i = 0; $i < 6; $i++): $top_stories->the_post(); ?>
              <?php get_template_part( 'template-parts/homepage-top-story-side' ); ?>
            <?php endfor; ?>
          </ul>
        </div>
        <div id="homepage-more-stories-sidebar">
          <p>put membership image here. Possibly a sidebar thing?</p>
        </div>
      </div>

      <div id="homepage-featured" class="homepage-fullwidth group">
        <?php $featured_story = z_get_zone_query('homepage_featured', array(
          'posts_per_page' => 1,
        ) ); 
        $featured_story->the_post();
        $fullwidth_title = 'Featured';
        ?>
        <?php include(locate_template( 'template-parts/homepage-fullwidth.php')); ?>
      </div>

      <div id="homepage-sections" class="group">
        <ul id="homepage-sections-list">
          <li class="homepage-section">
            <h2 class="promo">
              Politics
            </h2>
            <ul class="homepage-section-list">
              <?php $pol_query = new WP_Query(array(
                'category_name' => 'politics',
                'post_type' => array('mj_article', 'mj_fullwidth'),
                'posts_per_page' => 2,
              ) ); 
              $pol_query->the_post();
              get_template_part( 'template-parts/homepage-section-first');
              $pol_query->the_post();
              get_template_part( 'template-parts/homepage-section');
              ?>
            </ul>
          </li> 
          <li class="homepage-section">
            <h2 class="promo">
              Environment
            </h2>
            <ul class="homepage-section-list">
              <?php $pol_query = new WP_Query(array(
                'category_name' => 'environment',
                'post_type' => array('mj_article', 'mj_fullwidth'),
                'posts_per_page' => 2,
              ) ); 
              $pol_query->the_post();
              get_template_part( 'template-parts/homepage-section-first');
              $pol_query->the_post();
              get_template_part( 'template-parts/homepage-section');
              ?>
            </ul>
          </li> 
          <li class="homepage-section">
            <h2 class="promo">
              Media
            </h2>
            <ul class="homepage-section-list">
              <?php $pol_query = new WP_Query(array(
                'category_name' => 'media',
                'post_type' => array('mj_article', 'mj_fullwidth'),
                'posts_per_page' => 2,
              ) ); 
              $pol_query->the_post();
              get_template_part( 'template-parts/homepage-section-first');
              $pol_query->the_post();
              get_template_part( 'template-parts/homepage-section');
              ?>
            </ul>
          </li> 
          <li class="homepage-section">
            <h2 class="promo">
              Media FIXME!! is a category rn
            </h2>
            <ul class="homepage-section-list">
              <?php $pol_query = new WP_Query(array(
                'category_name' => 'food',
                'post_type' => array('mj_article', 'mj_fullwidth'),
                'posts_per_page' => 2,
              ) ); 
              $pol_query->the_post();
              get_template_part( 'template-parts/homepage-section-first');
              $pol_query->the_post();
              get_template_part( 'template-parts/homepage-section');
              ?>
            </ul>
          </li> 
          <li class="homepage-section">
            <h2 class="promo">
              Crime & Justice FIXME!! is a category rn
            </h2>
            <ul class="homepage-section-list">
              <?php $pol_query = new WP_Query(array(
                'category_name' => 'crime_&_justice',
                'post_type' => array('mj_article', 'mj_fullwidth'),
                'posts_per_page' => 2,
              ) ); 
              $pol_query->the_post();
              get_template_part( 'template-parts/homepage-section-first');
              $pol_query->the_post();
              get_template_part( 'template-parts/homepage-section');
              ?>
            </ul>
          </li> 
        </ul>
      </div>

      <div id="homepage-kdrum" class="group">
        <div id="homepage-kdrum-side">
          <h2 class="promo">
            Kevin Drum
          </h2>
          <img src="/wp-content/themes/motherjones/img/KEVIN.png"></img>
          <ul id="kdrum-post-list">
            <?php $kdrum = new WP_Query(array( 
                'tax_query' => array( array(
                  'taxonomy' => 'mj_blog_type',
                  'field' => 'slug',
                  'terms' => 'kevin-drum',
                )  ), 
                'post_type' => 'mj_blog_type',
                'posts_per_page' => 4,
              ) ); 
              while ( $kdrum->have_posts() ) {
                $kdrum->the_post(); 
                get_template_part( 'template-parts/homepage-kdrum-story' );
              }
            ?>
        </div>
        <div id="homepage-kdrum-ad">
          <p>put an ad here</p>
        </div>
      </div>

      <div id="homepage-exposure" class="homepage-fullwidth group">
        <?php $exposure_story = new WP_Query(array(
            'tax_query' => array( array(
              'taxonomy' => 'mj_media_type',
              'field' => 'slug',
              'terms' => 'photoessays',
            )  ), 
            'post_type' => array('mj_article', 'mj_fullwidth'),
            'posts_per_page' => 1,
          ) ); 
          $exposure_story->the_post();
          $fullwidth_title = 'Exposure';
        ?>
        <?php include(locate_template( 'template-parts/homepage-fullwidth.php')); ?>
      </div>

      <div id="homepage-second-ad" class="group">
        <p>FIXME put an ad here</p>
      </div>

      <div id="homepage-investigations" class="group">
        <h2 class="promo">Investigations</h2>
        <ul id="homepage-investigations-list">
          <?php $investigations = new WP_Query(array(
              'tax_query' => array( array(
                'taxonomy' => 'mj_primary_tag',
                'field' => 'slug',
                'terms' => 'investigations',
              )  ), 
              'post_type' => array('mj_article', 'mj_fullwidth'),
              'posts_per_page' => 4,
            ) ); 
            while ( $investigations->have_posts() ) {
              $investigations->the_post(); 
              get_template_part( 'template-parts/homepage-investigations');
            }
          ?>
        </ul>
      </div>

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
