<ul class="author-bios article end">
  <?php foreach (get_post_field( 'byline', get_the_ID() )['authors']  as $author_id):
    $author = get_post_custom( $author_id );
  ?>
      <li class="author-bio">
        <div class="author-image">
          <?php
            print wp_get_attachment_image( $author['image'][0], 
              array('80', '80')
            );
          ?>
        </div>
        <div class="author-data">
          <span class="author-bio byline">
            <?php print get_the_title( $author_id ); ?>
            <a href="https://twitter.com/@<?php print $author['twitter'][0] ?>">
              <i class="fa fa-twitter fw"></i>
            </a>
          </span>
          <p class="author-bio-text">
            <?php print $author['short_bio'][0] ?>
          </p>
        </div>
      </li>
  <?php endforeach ?>
</ul>
