<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains comments and the comment form.
 *
 * @package WordPress
 * @subpackage MotherJones
 * @since MotherJones 1.0
 */

$canonical_disqus_url = urlencode(
  get_site_url() . '/node/' . get_the_ID()
); 

?>
<div id="disqus-container">
  <div id="disqus_thread"></div>
  <div id="disqus-noscript" 
    onclick="display_disqus(); jQuery(this).addClass('hidden'); return false;">
      <a href="http://motherjones.com.disqus.com/?url=<?php 
         print urlencode(
           get_site_url() . '/node/' . get_the_ID()
         ); 
      ?>">
          <i class="fa fa-plus"></i>view comments
      </a>
  </div>
</div>
<?php wp_enqueue_script(
    'mj_disqus_js', get_template_directory_uri() . '/js/mj_disqus.js'
); ?>
<script>
  var disqus_config = function () {
    this.page.url = '<?php print get_site_url() . '/node/' . get_the_ID() ?>';  // Replace PAGE_URL with your page's canonical URL variable
    this.page.identifier = '<?php print '/node/' . get_the_ID(); ?>'; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
  };
</script>
