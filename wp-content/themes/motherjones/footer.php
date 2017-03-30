<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */
?>

		</main>
	</div><!-- #page -->

	<footer id="colophon" class="site-footer" role="contentinfo">

      <a href="/" id="footer-logo">
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/MJ_comp_grey.png" alt="Mother Jones" />
      </a>
      <ul id="footer-social">
          <li class="circled-icon toolbar-btn fblike">
              <a href="https://www.facebook.com/motherjones">
                  <i class="fa fa-facebook fw"></i>
              </a>
          </li>
          <li class="circled-icon toolbar-btn tweet">
              <a href="https://twitter.com/motherjones">
                  <i class="fa fa-twitter fw"></i>
              </a>
          </li>
          <li class="circled-icon toolbar-btn newsletter">
              <a href="/about/interact-engage/free-email-newsletter" class="hover-newsletter">
                <i class="fa fa-envelope fw"></i>
              </a>
          </li>
      </ul>
      <ul id="footer-list">
          <li>
              <a href="/about">
              	About Us
              </a> |
							<a href="http://store.motherjones.com/?utm_source=motherjones&utm_medium=footer&utm_content=orangefooterlink&utm_campaign=evergreen">
								Store
							</a>
          </li>
          <li>
              <a href="/about/support">
              	Donate
              </a> |
							<a href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN4&base_country=US">
              	Subscribe
              </a>
          </li>
          <li>
              <a href="/about/subscriptions/customer-service">
              	Customer Service
              </a>
          </li>
          <li>
              <a href="/about/advertising/contact-form">
              	Advertise
              </a>
          </li>
      </ul>
      <div id="copyright">
          <p>
          	Copyright &copy;2016 Mother Jones and the Foundation for National Progress. All Rights Reserved.
         	</p>
          <p>
              <a href="/about/terms">Terms of Service</a>
              <a href="/about/privacy-policy">Privacy Policy</a>
              <a href="/about/contact">Contact Us</a>
          </p>
      </div>

      <script language="javascript">
      <!--  //FIXME make this check to see if it's an article or blogpost.
      if ((typeof MJ_HideInContentAds === 'undefined') && (!(jQuery(".inline-right")[0])) && (!(jQuery(".inline-subnav")[0])))  {
        ad_code({
          desktop: true,
          placement: 'InContentAdUnit',
          height: 16,
          doc_write: true,
        });
      }

      if (typeof MJ_HideOverlayAds === 'undefined') {
        ad_code({
          desktop: true,
          placement: 'overlay',
          height: 67,
          doc_write: true,
        });
      }
      //-->
      </script>

      <div id="bottom-donate" style="display:none">
        <p>
          We noticed you have an ad blocker on.
          Support nonprofit investigative reporting by pitching in a few
          bucks.
          <a href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGZS1A&extra_don=1&abver=A">DONATE</a>
          <span onclick="jQuery('#bottom-donate').remove();">X</span>
        </p>
      </div>

      <?php dynamic_sidebar( 'page-end' ); ?>
	</footer><!-- .site-footer -->
	<?php wp_footer(); ?>
</body>
</html>
