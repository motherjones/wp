<ul id="prev-next">
  <li class="previous">
    <?php echo previous_post_link(
      ' <span class="label">Previous:</span> %link',
       '%title',
       TRUE,
       ' ',
       'mj_blog_type' ); ?>
  </li>
  <li class="next">
    <?php echo next_post_link(
      ' <span class="label">Next:</span> %link',
       '%title',
       TRUE,
       ' ',
       'mj_blog_type' ); ?>
  </li>
</ul>
