<li class="article-item">
  i am an articleish thing
  <a href="<?php print esc_url( get_permalink() ); ?>">
    <?php twentysixteen_post_thumbnail(); ?>
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
