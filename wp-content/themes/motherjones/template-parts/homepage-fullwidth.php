<div class="article-image">
  <a href="<?php print esc_url( get_permalink() ); ?>">
    <?php print wp_get_attachment_image( 
      get_post_field( 'master_image', get_the_ID() )['master_image'],
      'large_990'
    ); ?>
  </a>
</div>
<h2 class="homepage-fullwidth-section-label promo">
    <?php print $fullwidth_title; ?>
</h2>
<p class="homepage-art-byline">
  <?php print get_post_field( 'master_image', get_the_ID() )['master_image_byline']; ?>
</p>
<div class="article-data">
    <a href="<?php print esc_url( get_permalink() ); ?>">
      <?php the_title(); ?>
    </a>
  </h3>
  <p class="byline">
    <?php print mj_byline( get_the_ID() ); ?>
  </p>
</div>