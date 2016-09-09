<li class="top-article-item group">
  <div class="article-image">
    <a href="<?php the_permalink(); ?>">
      <?php 
      $master = get_post_meta(get_the_ID(), 'master_image' )[0];
      print_r($master);
      print wp_get_attachment_image( 
        $master['master_image'],
        'article_top'
      ); ?>
    </a>
  </div>
  <div class="article-data">
    <h3 class="hed">
      <a href="<?php print esc_url( get_permalink() ); ?>">
        <?php print get_post_meta(get_the_ID(), 'alt' )['alt_title']
                  ? get_post_meta(get_the_ID(), 'alt' )['alt_title']
                  : get_the_title(); ?>
      </a>
    </h3>
    <p class="byline">
      <?php print mj_byline( get_the_ID() ); ?>
    </p>
  </div>
</li>

