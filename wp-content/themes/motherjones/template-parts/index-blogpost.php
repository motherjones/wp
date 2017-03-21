<article class="blog">
	<div class="entry-header blog">
		<h1 class="blog hed">
			<a href="<?php print esc_url( get_permalink() ); ?>">
				<?php the_title(); ?>
			</a>
		</h1>
		<?php
			if ( ! empty( $meta['mj_dek'][0] ) ) {
				printf(
					'<h3 class="dek">%s</h3>',
					esc_html( $meta['mj_dek'][0] )
				);
			}
		?>
		<p class="byline-dateline">
			<span class="byline">
				<?php print mj_byline( get_the_ID() ); ?>
			</span>
			<span class="dateline">
				<?php print mj_dateline( get_the_ID() ); ?>
			</span>
		</p>
	</div><!-- .entry-header -->

	<?php
		if ( isset( $meta['css'][0] ) ) {
			printf(
				'<style>%s</style>',
				esc_html( $meta['css'][0] )
			);
		}
		largo_hero();
		the_content();
	?>

	<footer class="entry-footer">
		<?php mj_share_tools( 'blog' );?>
	</footer>
	<?php
		if ( ! empty( $meta['js'][0] ) ) {
			printf(
				'script>%s</script>',
				$meta['js'][0]
			);
		}
	?>
</article>
