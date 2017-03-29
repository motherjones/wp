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

      <!-- Quantcast Tag -->
      <script type="text/javascript">
      var _qevents = _qevents || [];
      (function() {
      var elem = document.createElement('script');
      elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
      elem.async = true;
      elem.type = "text/javascript";
      var scpt = document.getElementsByTagName('script')[0];
      scpt.parentNode.insertBefore(elem, scpt);
      })();
      _qevents.push({
      qacct:"p-Be0QSW1bbb6Qv"
      });
      </script>
      <noscript>
      <div style="display:none;">
      <img src="//pixel.quantserve.com/pixel/p-Be0QSW1bbb6Qv.gif" border="0" height="1" width="1" alt="Quantcast"/>
      </div>
      </noscript>
      <!-- End Quantcast tag -->

			<!-- Begin comScore Tag -->
			<script>
				var _comscore = _comscore || [];
				_comscore.push({ c1: "2", c2: "8027488" });
				(function() {
					var s = document.createElement("script"), el = document.getElementsByTagName("script")[0]; s.async = true;
					s.src = (document.location.protocol == "https:" ? "https://sb" : "http://b") + ".scorecardresearch.com/beacon.js";
					el.parentNode.insertBefore(s, el);
				})();
			</script>
			<noscript>
					<img src="http://b.scorecardresearch.com/p?c1=2&c2=8027488&cv=2.0&cj=1" />
			</noscript>
			<!-- End comScore Tag -->

			<!-- Begin Chartbeat Tag -->
			<script type='text/javascript'>
					var _sf_async_config={};
					/** CONFIGURATION START **/
					_sf_async_config.uid = 10683;
					_sf_async_config.domain = 'motherjones.com';
					<?php
					if ( is_singular() ) {
					?>
						_sf_async_config.sections = '<?php print esc_html(get_the_category(get_the_ID())[0]->name); ?>';
						<?php 
						if ( function_exists( 'coauthors' ) ) {
							$byline = coauthors( ', ', null, null, null, false );
						} else {
							$byline = get_the_author();
						}
						?>
						_sf_async_config.authors = "<?php echo esc_html($byline); ?>";
					<?php 
					} ?>
					/** CONFIGURATION END **/
					(function(){
						function loadChartbeat() {
							window._sf_endpt=(new Date()).getTime();
							var e = document.createElement('script');
							e.setAttribute('language', 'javascript');
							e.setAttribute('type', 'text/javascript');
							e.setAttribute('src',
								 (('https:' == document.location.protocol) 
								? 'https://a248.e.akamai.net/chartbeat.download.akamai.com/102508/' 
								: 'http://static.chartbeat.com/') +
								 'js/chartbeat.js');
							document.body.appendChild(e);
						}
						var oldonload = window.onload;
						window.onload = (typeof window.onload != 'function') ?
							 loadChartbeat : function() { oldonload(); loadChartbeat(); };
					})();
			</script>
			<!-- End Chartbeat Tag -->

			<!-- Begin ga (Google Analytics) Tag -->
			<script>
				(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

				ga('create', 'UA-2458520-1', 'auto');
				ga('send', 'pageview');
			</script>
			<!-- End ga (Google Analytics) Tag -->


      <?php dynamic_sidebar( 'page-end' ); ?>
	</footer><!-- .site-footer -->
	<?php wp_footer(); ?>
</body>
</html>
