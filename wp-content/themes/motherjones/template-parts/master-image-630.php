<?php print_r(get_post_meta( get_the_ID(), 'master_image' )[0]); ?>
<?php if (get_post_meta( get_the_ID(), 'master_image' )[0]['master_image']
      && !get_post_meta( get_the_ID(), 'master_image' )[0]['master_image_suppress']
): ?>
  <div class="article-master-image group">
    <?php 
    $master = get_post_meta(get_the_ID(), 'master_image' )[0];
    print wp_get_attachment_image( 
      $master['master_image'],
      'article_top'
    ); ?>
    <p class="master-image-data">
      <span class="master-image-caption">
        <?php print $master['master_image_caption']; ?>
      </span>
      <span class="photo-byline">
        <?php print $master['master_image_byline']; ?>
      </span>
    </p>
  </div>
<?php endif; ?>
