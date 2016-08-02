<?php if (get_post_field( 'master_image', get_the_ID() )['master_image']): ?>
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
<?php endif; ?>
