<?php global $meta; ?>
<header  class="entry-header">
  <?php the_title( '<h1 class="hed">', '</h1>' ); ?>
  <?php
    if ( $meta['mj_dek'][0] && ! mj_is_article_type( 'blogpost', $post->ID ) ) {
      printf(
        '<h3 class="dek">%s</h3>',
        $meta['mj_dek'][0]
      );
    }
  ?>
  <p class="byline-dateline">
    <span class="byline">
      <?php print mj_byline( get_the_ID() ); ?>
    </span>
    <span class="dateline">
      <?php print mj_dateline( get_the_ID() ); ?>
    </span>
  </p>
  <?php mj_share_tools( 'top' ); ?>
</header><!-- .entry-header -->
