<ul class="author-bios article end">
  <?php foreach (get_post_field( 'byline', get_the_ID() )['authors']  as $author_id):
    $author = get_post_custom( $author_id );
  ?>
      <li class="author-bio">
        <?php
          print_r($author['image']);
          print wp_get_attachment_image( $author['image']['id'], 
            '(max-width: 100px)'
          );
        ?>
        <h3 class="author-bio byline">
          <?php print get_the_title( $author_id ); ?>
          <a class="social"
             href="https://twitter.com/@<?php print $author['twitter'][0] ?>">
            <i class="fa fa-twitter fw"></i>
          </a>
        </h3>
        <p class="author-bio-text">
          <?php print $author['short_bio'][0] ?>
        </p>
      </li>
  <?php endforeach ?>
</ul>
