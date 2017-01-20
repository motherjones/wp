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

<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 941756981;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/941756981/?value=0&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<script type="text/javascript">

$(document).ready(function() {
  var gaEventCategory = null;
  var bodyClasses = $('body').attr('class');
  var gaEventAction = 'LinkClick';
  var bodyClassesArray = bodyClasses.split(' ');
  
  if (jQuery.inArray('front', bodyClassesArray) != -1) {
    gaEventCategory = 'areaTracking_front';
    
    var areaSelectorArray = ['body.front .view-display-id-panel_pane_1 ',
    'body.front .center-wrapper-above .pane-frontpage-stories',
    'body.front .pane-views-photoessay-block-3 ',
    'body.front .pane-motherjones-custom-7 ',
    'body.front contributors: .pane-motherjones-custom-6 ',
    'body.front .center-wrapper-below .pane-frontpage-stories',
    'body.front .pane-views-feedback-block-1 ',
    'body.front #mymojo ',
    'body.front .view-most-must-read ',
    'body.front #disqus-mostpopular ',
    'body.front #block-block-4 ',
    'body.front #block-motherjones_custom-8 ',
    'body.front #block-views-alumni_love-block ',
    'body.front #block-views-interns-block_1 ',
    'body.front #front-page-article-lists ',
    'body.front #footerlinks '
    ];
  } else if (jQuery.inArray('node-type-article', bodyClassesArray) != -1) {
    gaEventCategory = 'areaTracking_article';
    
    var areaSelectorArray = ['body.node-type-article #node-header',
    'body.node-type-article #node-body-top',
    'body.node-type-article #node-body-bottom',
    'body.node-type-article #node-footer',
    'body.node-type-article #related-articles',
    'body.node-type-article #mymojo',
    'body.node-type-article div.blockname-block-views-todays-top-stories',
    'body.node-type-article div.view-most-must-read',
    'body.node-type-article div.disqus-mostpopular',
    'body.node-type-article div.blockname-block-block-intune--inprint-sidebar-block',
    'body.node-type-article #ticker',
    'body.node-type-article #primary',
    'body.node-type-article #block-views-photoessay-block_1',
    'body.node-type-article #footerlinks'
    ];
  } else if (jQuery.inArray('node-type-blogpost', bodyClassesArray) != -1) {

    gaEventCategory = 'areaTracking_blogpost';
    
    var areaSelectorArray = ['body.node-type-blogpost #node-header',
    'body.node-type-blogpost #blog-nav',
    'body.node-type-blogpost #node-body-top',
    'body.node-type-blogpost #node-body-bottom',
    'body.node-type-blogpost #node-footer',
    'body.node-type-blogpost #related-articles',
    'body.node-type-blogpost #mymojo',
    'body.node-type-blogpost div.blockname-block-views-todays-top-stories',
    'body.node-type-blogpost div.view-most-must-read',
    'body.node-type-blogpost div.disqus-mostpopular',
    'body.node-type-blogpost div.blockname-block-block-intune--inprint-sidebar-block',
    'body.node-type-blogpost #ticker',
    'body.node-type-blogpost #primary',
    'body.node-type-blogpost #block-views-photoessay-block_1',
    'body.node-type-blogpost #footerlinks'
    ];
  } else if (jQuery.inArray('node-type-photoessay-image', bodyClassesArray) != -1) {

    gaEventCategory = 'areaTracking_photoessay-image';
    
    var areaSelectorArray = ['body.node-type-photoessay-image .sidebar-right a.photoessay-forward',
    'body.node-type-photoessay-image .sidebar-right a.photoessay-back',
    'body.node-type-photoessay-image .sidebar-right a.photoessay-rewind',
    'body.node-type-photoessay-image .photoessay-controls a.photoessay-forward',
    'body.node-type-photoessay-image .photoessay-controls a.photoessay-back',
    'body.node-type-photoessay-image .photoessay-controls a.photoessay-rewind',
    'body.node-type-photoessay-image #related-articles',
    'body.node-type-photoessay-image div.blockname-block-views-todays-top-stories',
    'body.node-type-photoessay-image #primary',
    'body.node-type-photoessay-image #footerlinks',
    'body.node-type-photoessay-image a.colorbox-thumbs',
    'body.node-type-photoessay-image a.larger-image-link',
    'body.node-type-photoessay-image a.see-all',
    'body.node-type-photoessay-image .photoessay-essay a',
    'body.node-type-photoessay-image #node-footer',
    'body.node-type-photoessay-image #node-footer div.fblike_btn',
    'body.node-type-photoessay-image .photoessay-share-buttons',
    'body.node-type-photoessay-image .photoessay-share-buttons div.fblike_btn',
    'body.node-type-photoessay-image .sidebar-right .view-id-photoessay',
    'body.node-type-photoessay-image #block-block-60',
    'body.node-type-photoessay-image #block-block-396'
    ];
  } else if (jQuery.inArray('page-politics', bodyClassesArray) != -1) {
    gaEventCategory = 'areaTracking_politics';
    
    var areaSelectorArray = ['body.page-politics div.views-row-1',
    'body.page-politics div.view-section-page-articles',
    'body.page-politics #mymojo',
    'body.page-politics div.blockname-block-views-todays-top-stories',
    'body.page-politics div.view-most-must-read',
    'body.page-politics div.disqus-mostpopular',
    'body.page-politics div.blockname-block-block-intune--inprint-sidebar-block',
    'body.page-politics #ticker',
    'body.page-politics #primary',
    'body.page-politics #footerlinks'
    ];
  }
  if (gaEventCategory != null) {
    for (var selector in areaSelectorArray) {
      $(areaSelectorArray[selector] + ' a').click(function(selectorString) { return function(){ _gaq.push(['_trackEvent', gaEventCategory, gaEventAction, selectorString]); }}(areaSelectorArray[selector]));
      
    }
  }
});
</script>

<!--Ad code for in content ad unit.  Uses jquery to only load on pages that do not have divs with a class named inline or inline-nav -->
<script language="javascript"> 
<!-- 
if ((typeof MJ_HideInContentAds === 'undefined') && (!($(".inline-right")[0])) && (!($(".inline-subnav")[0])))  {
  adtech_code('InContentAdUnit',16);
}    
 
if (typeof MJ_HideOverlayAds === 'undefined') {
    adtech_code('overlay',67);
}    
//--> 
</script> 
<!-- End of ad code for in content Tag -->

<div id="bottom-donate" style="display:none">
  <p>
    We noticed you have an ad blocker on.
    Support nonprofit investigative reporting by pitching in a few
    bucks.
    <a href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGZS1A&extra_don=1&abver=A">DONATE</a>
    <span onclick="$('#bottom-donate').remove();">X</span>
  </p>
</div>
<?php dynamic_sidebar( 'page-end' ); ?>
</body>
</html>
