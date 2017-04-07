<?php

$hostname="localhost";
$username="root";
$password=$argv[1];
$d6_db = "mjd6";
$wp_db = "pantheon_wp";
$FILEDIR_ABS = "http://dev-mjwordpress.pantheonsite.io/wp-content/uploads/";
$FILEDIR = "wp-content/uploads/";


$d6 = new PDO("mysql:host=$hostname;dbname=$d6_db", $username, $password);

$wp = new PDO("mysql:host=$hostname;dbname=$wp_db", $username, $password);

$option_replace = $wp->prepare('
REPLACE INTO pantheon_wp.wp_options
(option_name, option_value, autoload)
VALUES ( ?, ?, "yes" )
');

$wp->beginTransaction();
$option_replace->execute(Array('blogname', 'Mother Jones Magazine'));
$option_replace->execute(Array(
  'blogdescription',
  //'Mother Jones is a leading independent news organization, featuring investigative and breaking news reporting on politics, the environment, human rights, and culture. Winner of six National Magazine Awards and the Online News Association Award for Online Topical Reporting.'
  'Smart, fearless journalism'
));
$wp->commit();

// Set default theme to motherjones
$wp->beginTransaction();
$option_replace->execute(Array('template', 'motherjones'));
$option_replace->execute(Array('stylesheet', 'motherjones'));
$wp->commit();

// set posts per page
$wp->beginTransaction();
$option_replace->execute(Array('posts_per_page', 20));
$wp->commit();

// set facebook instant ads and analytics
$wp->beginTransaction();
$option_replace->execute(Array(
  'instant-articles-option-ads',
  '{"ad_source":"iframe","fan_placement_id":"","iframe_url":"\/\/adserver.adtechus.com\/adiframe\/3.0\/5443.1\/4101495\/0\/170\/ADTECH;target=_blank","embed_code":"<script src=\"http:\/\/adserver.adtechus.com\/addyn\/3.0\/5443.1\/4101495\/0\/170\/ADTECH;loc=700;\"><\/script>","dimensions":"300x250"}'
));
$analytics_code = <<<'HTML'
{"embed_code_enabled":"1","embed_code":"<script>\r\n  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){\r\n  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),\r\n  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)\r\n  })(window,document,'script','https:\/\/www.google-analytics.com\/analytics.js','ga');\r\n  ga('create', 'UA-2458520-1', 'auto');\r\n  var dimensionValue = 'Facebook_Instant';\r\n  ga('set', 'dimension2', dimensionValue);\r\n  ga('set', 'campaignSource', 'Facebook');\r\n  ga('set', 'campaignMedium', 'Social Instant Article');\r\n  ga('send', 'pageview');\r\n  ga('send', 'event', 'Author', \"$byline\", window.location.href);\r\n\r\n    var _sf_async_config={};\r\n    \/** CONFIGURATION START **\/ \r\n    _sf_async_config.uid = 10683;\r\n    _sf_async_config.domain = 'motherjones.com';\r\n    _sf_async_config.sections = ''; \r\n    _sf_async_config.authors = ''; \r\n    \/** CONFIGURATION END **\/ \r\n    window._sf_endpt = (new Date()).getTime();\r\n var _comscore = _comscore || [];\r\n _comscore.push({ c1: \"2\", c2: \"8027488\", comscorekw: \"fbia\" });\r\n (function() {\r\n   var s = document.createElement(\"script\"), el = document.getElementsByTagName(\"script\")[0]; s.async = true;\r\n   s.src = (document.location.protocol == \"https:\" ? \"https:\/\/sb\" : \"http:\/\/b\") + \".scorecardresearch.com\/beacon.js\";\r\n   el.parentNode.insertBefore(s, el);\r\n })();\r\n\r\n<\/script>"}
HTML;
$option_replace->execute(Array( 
  'instant-articles-option-analytics',
	$analytics_code
));
$wp->commit();

// set permalink structure
$wp->beginTransaction();
$option_replace->execute(Array(
  'permalink_structure',
  '/%category%/%year%/%monthnum%/%postname%/'
));
$option_replace->execute(Array( 'tag_base', '/topics' ));
$wp->commit();


//set image sizes
$wp->beginTransaction();

$option_replace->execute(Array( 'thumbnail_size_h', '117' ));
$option_replace->execute(Array( 'thumbnail_size_w', '208' ));
$option_replace->execute(Array( 'thumbnail_crop', '1' ));

$option_replace->execute(Array( 'medium_size_h', '273' ));
$option_replace->execute(Array( 'medium_size_w', '485' ));
$option_replace->execute(Array( 'medium_size_crop', '1' ));

$option_replace->execute(Array( 'medium_large_size_h', '354' ));
$option_replace->execute(Array( 'medium_large_size_w', '630' ));
$option_replace->execute(Array( 'medium_large_size_crop', '1' ));

$option_replace->execute(Array( 'large_size_h', '557' ));
$option_replace->execute(Array( 'large_size_w', '990' ));
$option_replace->execute(Array( 'large_size_crop', '1' ));

$wp->commit();


// Activate plugins
$active_plugins = Array(
  'mfi-reloaded-master/mfi-reloaded.php',
  'coauthors/co-authors-plus.php',
  'redirection/redirection.php',
  'zoninator/zoninator.php',
  'mj_custom/mj_custom.php',
  'disqus-conditional-load/disqus-conditional-load.php',
  'fb-instant-articles/facebook-instant-articles.php',
  'bwp-google-xml-sitemaps/bwp-simple-gxs.php',
);
$wp->beginTransaction();

$option_replace->execute(Array(
  'active_plugins',
  serialize($active_plugins)
));
$wp->commit();


?>
