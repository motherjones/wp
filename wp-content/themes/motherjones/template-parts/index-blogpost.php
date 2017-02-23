<article class="blog">
  <div class="entry-header blog">
    <h1 class="blog hed">
      <a href="<?php print esc_url( get_permalink() ); ?>">
        <?php the_title(); ?>
      </a>
    </h1>
    <?php if ( get_post_meta(get_the_ID(), 'dek' )[0] ): ?>
      <h3 class="dek">
        <?php print get_post_meta( get_the_ID(), 'dek' )[0]; ?>
      </h3>
    <?php endif ?>
    <p class="byline-dateline">
      <span class="byline">
        <?php print mj_byline( get_the_ID() ); ?>
      </span>
      <span class="dateline">
        <?php print mj_dateline( get_the_ID() ); ?>
      </span>
    </p>
  </div><!-- .entry-header -->
  <style>
    <?php print get_post_meta( get_the_ID(), 'css' )[0]; ?>
  </style>

  <?php get_template_part( 'template-parts/master-image-630' ); ?>

  <?php print get_post_meta( get_the_ID(), 'body' )[0]; ?>
  <script>
    <?php print get_post_meta( get_the_ID(), 'js' )[0]; ?>
  </script>

  <footer class="entry-footer">
    <div class="social-container blog">
      <ul class="social-tools blog">
        <li class="twitter">
          <?php print mj_flat_twitter_button( get_the_ID() ); ?>
        </li>
        <li class="facebook">
          <?php print mj_flat_facebook_button( get_the_ID() ); ?>
        </li>
        <li>FIXME add disqus button</li>
      </ul>
    </div>
  </footer>
</article>
