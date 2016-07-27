<div id="related-articles">
  <h2 class="promo">Related</h2>
    <ul class="related-articles-list">
      <?php 
        $related = get_post_field( 'related_articles', get_the_ID() ); 
        $related_query = new WP_Query(array(
          'post__in' => $related['relateds'],
          'post_type' => Array('mj_blog_post', 'mj_article', 'mj_full_width')
        ) );
        while ( $related_query->have_posts() ) : $related_query->the_post(); 
      ?>
<h1>lookin for an article</h1>

        <?php get_template_part( 'template-parts/standard-article-li' ); ?>

        WHY DON"T YOU PRING THE PART UGH!

      <?php endwhile; ?>
    </ul>
</div>
