<?php
/**
 * Enqueue all the scripts, styles, etc.
 *
 * @package    WordPress
 * @subpackage Mother_Jones
 * @since      Mother Jones 1.0
 */

if (! function_exists('mj_enqueue') ) {
    /**
     * Front end styles.
     */
    function mj_enqueue() 
    {
        $suffix = (MJ_DEBUG) ? '' : '.min';
        $version = '1.0';

        // Theme stylesheet.
        wp_enqueue_style(
            'mj-style',
            get_template_directory_uri() . '/css/style' . $suffix . '.css',
            null,
            $version
        );

        // Icons.
        wp_enqueue_style(
            'font-awesome',
            get_template_directory_uri() . '/css/font-awesome-4.6.3/css/font-awesome' . $suffix . '.css',
            null,
            $version
        );

        wp_enqueue_script(
            'mj-html5',
            get_template_directory_uri() . '/js/html5' . $suffix . '.js',
            array(),
            '3.7.3'
        );
        wp_script_add_data('mj-html5', 'conditional', 'lt ie 9');

        wp_enqueue_script(
            'mj-skip-link-focus-fix',
            get_template_directory_uri() . '/js/skip-link-focus-fix' . $suffix . '.js',
            array(),
            $version,
            true
        );

        if (is_singular() && wp_attachment_is_image() ) {
               wp_enqueue_script(
                   'mj-keyboard-image-navigation',
                   get_template_directory_uri() . '/js/keyboard-image-navigation' . $suffix . '.js',
                   array( 'jquery' ),
                   $version
               );
        }

        wp_enqueue_script(
            'mj-script',
            get_template_directory_uri() . '/js/functions' . $suffix . '.js',
            array( 'jquery' ),
            $version,
            true
        );
        wp_enqueue_script(
            'nav',
            get_template_directory_uri() . '/js/nav' . $suffix . '.js',
            array( 'jquery' ),
            $version,
            true
        );
        wp_enqueue_script(
            'video-embed',
            get_template_directory_uri() . '/js/video-embed' . $suffix . '.js',
            array( 'jquery' ),
            $version,
            true
        );
        wp_enqueue_script(
            'ad_code',
            get_template_directory_uri() . '/js/ad_code' . $suffix . '.js',
            array( 'jquery' ),
            $version
        );

        wp_localize_script(
            'mj-script', 'screenReaderText', array(
            'expand'   => __('expand child menu', 'mj'),
            'collapse' => __('collapse child menu', 'mj'),
            ) 
        );
    }
}
add_action('wp_enqueue_scripts', 'mj_enqueue');

/**
 * Enqueue admin JS and styles.
 *
 * @param string $hook the admin page we're viewing.
 */
function mj_admin_style( $hook ) 
{
    $suffix = (MJ_DEBUG) ? '' : '.min';
    $version = '1.0';
    if (( 'post.php' === $hook ) || ( 'post-new.php' === $hook ) ) {
        wp_enqueue_style(
            'mj-post-admin-style',
            get_template_directory_uri() . '/css/admin/post-admin' . $suffix . '.css',
            false,
            '1.0'
        );
    }
}
add_action('admin_enqueue_scripts', 'mj_admin_style');
/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 */
function mj_javascript_detection() 
{
    echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action('wp_head', 'mj_javascript_detection', 0);

/**
 * Output the trackers and whatnot when wp_footer is called.
 */
function mj_enqueue_footer_js() 
{
    // logged in users don't get tracked.
    if (is_user_logged_in() ) {
        return;
    }
    ?>
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
    if (is_singular() ) {
        ?>
       _sf_async_config.sections = '<?php print esc_html(get_the_category(get_the_ID())[0]->name); ?>';
        <?php
        if (function_exists('coauthors') ) {
            $byline = coauthors(', ', null, null, null, false);
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

  <!-- start adblock donate ask -->
  <script>
    function set_cookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    function get_cookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
    jQuery(document).ready(function() {
      if (typeof noadblocking === "undefined") {
          var checked = get_cookie('noad');
          if (!checked) {
            set_cookie('noad', true, 0.25, '/');
            jQuery('#bottom-donate').show();
            ga('send', 'event', 'AdblockDetected', 'DonateDisplayed', window.location.href);
          } else {
            ga('send', 'event', 'AdblockDetected', 'NoDonateDisplayed', window.location.href);
          }
      }
    });
  </script>
  <!-- end adblock donate ask -->

<?php
}
add_action('wp_footer', 'mj_enqueue_footer_js');
