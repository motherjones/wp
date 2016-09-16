<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

		</div><!-- .site-content -->

		<footer id="colophon" class="site-footer" role="contentinfo">

      <a href="/" id="footer-logo">
          <img src="/wp-content/themes/motherjones/img/MJ_comp_grey.png" alt="Mother Jones" />
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
              </a>
          </li>
          <li>
              <a href="/about/support">
                  Donate
              </a>
|                    <a href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN4&base_country=US">
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

		</footer><!-- .site-footer -->
</div><!-- .site -->

<?php wp_footer(); ?>

<script type="text/javascript" src="/wp-content/themes/motherjones/js/nav.js"></script>
<script type="text/javascript" src="/wp-content/themes/motherjones/js/jquery-3.1.0.min.js"></script>

<?php dynamic_sidebar( 'page-end' ); ?>
</body>
</html>
