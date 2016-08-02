<article class="blog">
  <div class="entry-header blog">
    <h1 class="blog hed">
      <a href="<?php print esc_url( get_permalink() ); ?>">
        <?php the_title(); ?>
      </a>
    </h1>
    <h3 class="dek">
      <?php print get_post_field( 'dek', get_the_ID() ); ?>
    </h3>
    <p class="byline-dateline">
      <span class="byline">
        <?php print mj_byline( get_the_ID() ); ?>
      </span>
      <span class="dateline">
        <?php print mj_dateline( get_the_ID() ); ?>
      </span>
    </p>
  </div><!-- .entry-header -->
    <?php print get_post_field( 'css', get_the_ID() ); ?>
    
    <?php get_template_part( 'template-parts/master-image-630' ); ?>

    <?php print get_post_field( 'body', get_the_ID() ); ?>
    <?php print get_post_field( 'js', get_the_ID() ); ?>

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
