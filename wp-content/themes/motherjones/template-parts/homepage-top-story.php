<div id="homepage-top-story" class="article-item">
  <div class="article-image">
    <a href="<?php print esc_url( get_permalink() ); ?>">
      <?php print wp_get_attachment_image(
        get_post_meta( get_the_ID(), 'master_image' )[0]['master_image'],
        'homepage_top_story'
      ); ?>
    </a>
  </div>
  <div class="article-data">
    <h1 class="hed">
      <a href="<?php print esc_url( get_permalink() ); ?>">
        <?php print get_post_meta( get_the_ID(), 'alt' )['mj_promo_hed']
                  ? get_post_meta( get_the_ID(), 'alt' )['mj_promo_hed']
                  : get_the_title(); ?>
      </a>
    </h1>
    <h4 class="dek">
      <a href="<?php print esc_url( get_permalink() ); ?>">
        <?php print get_post_meta( get_the_ID(), 'alt' )['mj_promo_dek']
                  ? get_post_meta( get_the_ID(), 'alt' )['mj_promo_dek']
                  : get_post_meta( get_the_ID(), 'dek' )[0]; ?>
      </a>
    </h4>
    <p class="byline">
      <?php print mj_byline( get_the_ID() ); ?>
    </p>
  </div>
  <p class="homepage-art-byline">
    <?php print get_post_meta( get_the_ID(), 'master_image' )[0]['master_image_byline']; ?>
  </p>
</div>
