<li class="article-item">
  <a href="<?php print esc_url( get_permalink() ); ?>">
<?php print_r(get_post_field( 'master_image', get_the_ID() )); ?>
    <?php print wp_get_attachment_image( 
      get_post_field( 'master_image', get_the_ID() )['master_image'],
      '208'
    ); ?>
  </a>
  <h3 class="hed">
    <a href="<?php print esc_url( get_permalink() ); ?>">
      <?php the_title(); ?>
    </a>
  </h3>
  <p class="byline">
    <?php print mj_byline( get_the_ID() ); ?>
  </p>
</li>
