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

// Set default theme to motherjones
//

$wp->beginTransaction();
$option_replace->execute(Array('template', 'motherjones'));
$option_replace->execute(Array('stylesheet', 'motherjones'));
$wp->commit();

// set permalink structure
$wp->beginTransaction();
$option_replace->execute(Array(
  'permalink_structure',
  '/%category%/%year%/%monthnum%/%postname%/'
));
$option_replace->execute(Array( 'tag_base', '/topics' ));
$wp->commit();


// Activate plugins
$active_plugins = Array(
  'mfi-reloaded-master/mfi-reloaded.php',
  'display-widgets/display-widgets.php',
  'coauthors/co-authors-plus.php',
  'redirection/redirection.php',
  'zoninator/zoninator.php',
  'mj_custom/mj_custom.php',
  'disqus-conditional-load/disqus-conditional-load.php',
  'fb-instant-articles/facebook-instant-articles.php',
);
$active_plugin_update = $wp->prepare('
UPDATE pantheon_wp.wp_options
SET option_value = ?
WHERE option_name = "active_plugins"
;
');
$wp->beginTransaction();
$option_replace->execute(Array(
  'active_plugins',
  serialize($active_plugins)
));


// redirect photoessay page
$redirect_item_insert = $wp->prepare('
INSERT INTO wp_redirection_items
(url, last_access, group_id, action_type, action_code, action_data, match_type)
VALUES (
"/photoessays", # source
FROM_UNIXTIME("1970-1-1 00:00:00"), #last access
1,
"url", #action type
301, # action code
"topics/photoessays", #destination action data
"url" #match type
)
;');


?>
