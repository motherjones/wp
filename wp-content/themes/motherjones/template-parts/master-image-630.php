
<?php print_r(get_post_meta( get_the_ID(), 'master_image' )); ?>
<h2>MASTER GOES HERE?</h2>
<?php print_r(get_post_meta( get_the_ID(), 'master_image' )['master_image']); ?>
is suppressed below
<?php print_r(get_post_meta( get_the_ID(), 'master_image' )['master_image_suppress']); ?>
<?php if (get_post_meta( get_the_ID(), 'master_image' )[0]['master_image']
      && !get_post_meta( get_the_ID(), 'master_image' )[0]['master_image_suppress']
): ?>
  <div class="article-master-image group">
    <?php 
    $master = unserialize(get_post_meta(get_the_ID(), 'master_image' ));
print_r($master);
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
