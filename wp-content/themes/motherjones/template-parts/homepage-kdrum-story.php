<li class="article-item">
  <div class="article-data">
    <h3 class="hed">
      <a href="<?php print esc_url( get_permalink() ); ?>">
        <?php the_title(); ?>
      </a>
    </h3>
    <p class="dateline">
      <?php print mj_dateline( get_the_ID() ); ?>
    </p>
  </div>
</li>

