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
