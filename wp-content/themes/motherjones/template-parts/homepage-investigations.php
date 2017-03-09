<li class="article-item">
  <div class="article-image">
    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'homepage_investigations' ); ?></a>
    </a>
  </div>
  <div class="article-data">
    <p class="homepage-art-byline">
      <?php print get_post_meta( get_the_ID(), 'master_image' )[0]['master_image_byline']; ?>
    </p>
    <h3 class="hed">
      <a href="<?php print esc_url( get_permalink() ); ?>">
        <?php
          if ( $hed = get_post_meta( get_the_ID(), 'mj_promo_hed', true ) ) {
            echo $hed;
          } else {
            the_title();
          }
        ?>
      </a>
    </h3>
    <p class="byline">
      <?php print mj_byline( get_the_ID() ); ?>
    </p>
  </div>
</li>
