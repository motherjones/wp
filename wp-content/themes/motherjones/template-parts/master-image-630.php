<?php if (get_post_meta( get_the_ID(), 'master_image' )['master_image']
      && !get_post_meta( get_the_ID(), 'master_image' )['master_image_suppress']
): ?>
  <div class="article-master-image group">
    <?php print wp_get_attachment_image( 
      get_post_meta( get_the_ID(), 'master_image' )['master_image'],
      'article_top'
    ); ?>
    <p class="master-image-data">
      <?php if ( get_post_meta( get_the_ID(), 'master_image' )['master_image_caption']
        ||  get_post_meta( get_the_ID(), 'master_image' )['master_image_byline'] ) : ?>
        <span class="master-image-caption">
          <?php print get_post_meta( get_the_ID(), 'master_image' )['master_image_caption']; ?>
        </span>
        <span class="photo-byline">
          <?php print get_post_meta( get_the_ID(), 'master_image' )['master_image_byline']; ?>
        </span>
      <?php endif; ?>
    </p>
  </div>
<?php endif; ?>
