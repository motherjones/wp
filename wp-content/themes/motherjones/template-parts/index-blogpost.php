<article class="blog">
  <header class="entry-header blog">
    <?php the_title( '<h1 class="blog hed">', '</h1>' ); ?>
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
  </header><!-- .entry-header -->
    <?php print get_post_field( 'css', get_the_ID() ); ?>
    
    <div class="article-master-image group">
      <?php print wp_get_attachment_image( 
        get_post_field( 'master_image', get_the_ID() )['master_image'],
        'article_top'
      ); ?>
      <p class="master-image-data">
        <span class="master-image-caption">
        </span>
        <span class="photo-byline">
        </span>
      </p>
    </div>

    <?php print get_post_field( 'body', get_the_ID() ); ?>
    <?php print get_post_field( 'js', get_the_ID() ); ?>

  <footer class="entry-footer">
    <div class="social-container blog bottom">
      <ul class="social-tools blog bottom">
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
