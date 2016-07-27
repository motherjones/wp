<div id="related-articles">
  <h2 class="promo">Related</h2>
    <ul class="related-articles-list">
      <?php 
        $related = get_post_field( 'related_articles', get_the_ID() ); 
        $related_query = new WP_Query(array(
          'post__in' => $related['relateds']
        ) );
        while ( $related_query->have_posts() ) : $related_query->the_post(); 
      ?>

        <?php get_template_part( 'standard-article-li', 'single' ); ?>

      <?php endwhile; ?>
    </ul>
</div>
