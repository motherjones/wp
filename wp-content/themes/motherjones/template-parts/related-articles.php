<div id="related-articles" class="group">
  <h2 class="promo">Related</h2>
    <ul class="related-articles-list">
      <?php 
        $related = get_post_meta( get_the_ID(), 'related_articles' ); 
        $related_query = new WP_Query(array(
          'post__in' => $related['relateds'],
          'post_type' => Array('mj_blog_post', 'mj_article', 'mj_full_width'),
          'posts_per_page' => 2
        ) );
        while ( $related_query->have_posts() ) : $related_query->the_post(); 
      ?>

        <?php get_template_part( 'template-parts/standard-article-li' ); ?>

      <?php endwhile; ?>
    </ul>
</div>
