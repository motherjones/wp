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

// Set default theme to motherjones
//

$wp->beginTransaction();
$wp->exec('
UPDATE pantheon_wp.wp_options
SET option_value = "motherjones"
WHERE option_name = "template"
;
');
$wp->commit();

$wp->beginTransaction();
$wp->exec('
UPDATE pantheon_wp.wp_options
SET option_value = "motherjones"
WHERE option_name = "stylesheet"
;
');
$wp->commit();

// set permalink structure
$wp->beginTransaction();
$wp->exec('
UPDATE pantheon_wp.wp_options
SET option_value = "/%category%/%year%/%monthnum%/%postname%/"
WHERE option_name = "permalink_structure"
;
');
$wp->commit();

// set topic page structure
$wp->beginTransaction();
$wp->exec('
UPDATE pantheon_wp.wp_options
SET option_value = "/topics"
WHERE option_name = "tag_base"
;
');
$wp->commit();

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

// Activate plugins
$active_plugins = Array(
  'mfi-reloaded-master/mfi-reloaded.php',
  'display-widgets/display-widgets.php',
  'coauthors/co-authors-plus.php',
  'redirection/redirection.php',
  'zoninator/zoninator.php',
  'mj_custom/mj_custom.php',
  'fb-instant-articles/facebook-instant-articles.php',
);
$active_plugin_update = $wp->prepare('
UPDATE pantheon_wp.wp_options
SET option_value = ?
WHERE option_name = "active_plugins"
;
');
$wp->beginTransaction();
$active_plugin_update->execute(Array(
  serialize($active_plugins)
));
$wp->commit();

?>
