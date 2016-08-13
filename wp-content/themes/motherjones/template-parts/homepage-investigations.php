<li class="article-item">
  <div class="article-image">
    <a href="<?php print esc_url( get_permalink() ); ?>">
      <?php print wp_get_attachment_image( 
        get_post_meta(get_the_ID(), 'master_image' )['master_image'],
        'homepage_investigations'
      ); ?>
    </a>
  </div>
  <div class="article-data">
    <p class="homepage-art-byline">
      <?php print get_post_meta( get_the_ID(), 'master_image' )['master_image_byline']; ?>
    </p>
    <h3 class="hed">
      <a href="<?php print esc_url( get_permalink() ); ?>">
        <?php print get_post_meta( get_the_ID(), 'alt')['alt_title']
                  ? get_post_meta( get_the_ID(), 'alt')['alt_title']
                  : get_the_title(); ?>
      </a>
    </h3>
    <p class="byline">
      <?php print mj_byline( get_the_ID() ); ?>
    </p>
  </div>
</li>
