<li class="article-item">
  <div class="article-image">
    <a href="<?php print esc_url( get_permalink() ); ?>">
      <?php print wp_get_attachment_image( 
        get_post_field( 'master_image', get_the_ID() )['master_image'],
        'homepage_investigations'
      ); ?>
    </a>
  </div>
  <div class="article-data">
    <p class="homepage-art-byline">
      <?php print get_post_field( 'master_image', get_the_ID() )['master_image_byline']; ?>
    </p>
    <h4 class="hed">
      <a href="<?php print esc_url( get_permalink() ); ?>">
        <?php the_title(); ?>
      </a>
    </h4>
    <p class="byline">
      <?php print mj_byline( get_the_ID() ); ?>
    </p>
  </div>
</div>

