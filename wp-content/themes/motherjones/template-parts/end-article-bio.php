<ul class="author-bios article end group">
  <?php $authors = get_coauthors( get_the_ID() );
  foreach ($authors  as $author): ?>
      <li class="author-bio">
        <div class="author-image">
          <?php
            print wp_get_attachment_image( $author->image[0], 
              array('100', '100')
            );
          ?>
        </div>
        <div class="author-data">
          <span class="author-bio byline">
            <a href="/author/<?php print $author->user_nicename; ?>">
              <?php print $author->display_name; ?>
            </a>
            <a href="https://twitter.com/@<?php 
             print get_user_meta($author->id, 'twitter', true); ?>">
              <i class="fa fa-twitter fw"></i>
            </a>
          </span>
          <p class="author-bio-text">
            <?php print get_user_meta($author->id, 'short_bio', true); ?>
          </p>
        </div>
      </li>
  <?php endforeach ?>
</ul>
