<ul class="author-bios article end">
  <?php foreach (get_post_field( 'byline' )['authors']  as $author): ?>
      <li class="author-bio">
        <?php
          echo wp_get_attachment_image( $author['image']['id'], 
            '(max-width: 100px)'
          );
        ?>
        <h3 class="author-bio">
          <?php print $author['title'] ?>
          <span class="position">
            <?php print $author['position'] ?>
          </span>
          <a href="https://twitter.com/<?php print $author['twitter'] ?>">
            <i class="fa-twitter" />
          </a>
        </h3>
        <p>
          <?php print $author['short-bio'] ?>
        </p>
      </li>
  <?php endforeach ?>
</ul>
