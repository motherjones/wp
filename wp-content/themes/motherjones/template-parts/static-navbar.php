<div id="static-navbar">

  <a name="navigation" id="navigation"></a>
  <ul class="menu">
				<?php
					/**
					 * Filter the default Mother Jones custom header sizes attribute.
					 *
					 * @since Mother Jones 1.0
					 *
					 * @param string $custom_header_sizes sizes attribute
					 * for Custom Header. Default '(max-width: 709px) 85vw,
					 * (max-width: 909px) 81vw, (max-width: 1362px) 88vw, 1200px'.
					 */
					$custom_header_sizes = apply_filters( 'twentysixteen_custom_header_sizes', '(max-width: 709px) 85vw, (max-width: 909px) 81vw, (max-width: 1362px) 88vw, 1200px' );
				?>
    <li class="first menu-item-home"><a href="/" title="Mother Jones Homepage">
      <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/MJ_comp.png" srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( 12 ) ); ?>" sizes="<?php echo esc_attr( $custom_header_sizes ); ?>" alt="Mother Jones">
    </a></li>
    <li class="menu-item-politics"><a href="/politics" title="Politics">Politics</a></li>
    <li class="menu-item-environment"><a href="/environment" title="Environment">Environment</a></li>
    <li class="menu-item-food"><a href="/food" title="">Food</a></li>
    <li class="menu-item-media"><a href="/media" title="Media">Media</a></li>
    <li class="menu-item-crime-justice"><a href="/crime-justice" title="">Crime &amp; Justice</a></li>
    <li class="menu-item-photos"><a href="/photoessays" title="Photos">Photos</a></li>
    <li class="menu-item-investigations"><a href="/topics/investigations" title="">Investigations</a></li>
    <li class="menu-item-magazine"><a href="http://www.motherjones.com/magazine" title="">Magazine</a></li>
    <li class="menu-item-subscribe"><a href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&amp;pub_code=MJM&amp;term_pub=MJM&amp;list_source=SEGYN&amp;base_country=US" title="Subscribe">Subscribe</a></li>
    <li class="last menu-item-donate"><a href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&amp;list_source=7HEGP001&amp;extra_don=1&amp;abver=A" title="Donate">Donate</a></li>
  </ul>

</div>
