<?php
/**
 * The template used for displaying page content
 *
 * @package    WordPress
 * @subpackage Mother_Jones
 * @since      Mother Jones 1.0
 */
?>

<header class="entry-header grid__col-12">
    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
</header><!-- .entry-header -->

<section id="post-<?php the_ID(); ?>" <?php post_class('item grid__col-12'); ?>>

    <article class="entry-content">
    <?php
    the_content();

    wp_link_pages(
        array(
        'before'      => '<div class="page-links"><span class="page-links-title">' . __('Pages:', 'mj') . '</span>',
        'after'       => '</div>',
        'link_before' => '<span>',
        'link_after'  => '</span>',
        'pagelink'    => '<span class="screen-reader-text">' . __('Page', 'mj') . ' </span>%',
        'separator'   => '<span class="screen-reader-text">, </span>',
        ) 
    );
    ?>
    </article><!-- .entry-content -->

    <?php edit_post_link('edit this post', '| <span class="edit-link">', '</span>'); ?>

</section><!-- #post-## -->
