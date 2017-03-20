<? $canonical_disqus_url = urlencode(
  get_site_url() . '/node/' . get_the_ID()
); ?>
<div id="disqus_thread"></div>
<div id="disqus-noscript" 
  onclick="displayDisqus(); $(this).addClass('hidden'); return false;">
    <a href="http://motherjones.com.disqus.com/?url=<? 
             print $canonical_disqus_url; ?>">
        <i class="icon-plus"></i>View Comments
    </a>
</div>
<?php wp_enqueue_script(
    'mj_disqus_js', get_template_directory_uri() . '/js/mj_disqus.js'
); ?>
<script>
  var disqus_config = function () {
    this.page.url = '<? print $canonical_disqus_url; ?>';  // Replace PAGE_URL with your page's canonical URL variable
    this.page.identifier = '<? print '/node/' . get_the_ID(); ?>'; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
  };
</script>
