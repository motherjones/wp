<?php
$hostname="localhost";
$username="root";
$password=$argv[1];
$d6_db = "mjd6";
$wp_db = "pantheon_wp";
$FILEDIR_ABS = "http://dev-mjwordpress.pantheonsite.io/wp-content/uploads/";
$FILEDIR = "";


$d6 = new PDO("mysql:host=$hostname;dbname=$d6_db", $username, $password);

$wp = new PDO("mysql:host=$hostname;dbname=$wp_db", $username, $password);

$author_insert = $wp->prepare('
INSERT INTO pantheon_wp.wp_users
(user_nicename, user_login, user_registered, display_name, user_pass)
VALUES (
  "testauthor", # NICENAME lowercase, - instead of space
  "testauthor", # login lowercase, no spaces
  FROM_UNIXTIME("1970-1-1 00:00:00"),
  "",
  "$P$BYuKSpgxoFA/xgFZ82KOJSbIIwKVhJ."
);
');
$wp->beginTransaction();
  $author_insert->execute();
  $author_id = $wp->lastInsertId();
  print($wp->lastInsertId());
  print "\n user id ^^ \n";
$wp->commit();

$roles_insert = $wp->prepare("
REPLACE INTO pantheon_wp.wp_usermeta
(meta_key, meta_value, user_id)
VALUES ( 
'wp_capabilities',
'a:1:{s:6:\"author\";s:1:\"1\";}',
?
)
;
");
$wp->beginTransaction();
  $roles_insert->execute(Array($author_id));
  print($wp->lastInsertId());
  print "\n user meta ^^ \n";
$wp->commit();

$level_insert = $wp->prepare("
REPLACE INTO pantheon_wp.wp_usermeta
(meta_key, meta_value, user_id)
VALUES ( 
'wp_user_level',
?,
?
)
;
");
$wp->beginTransaction();
  $level_insert->execute(Array(2, $author_id));
  print($wp->lastInsertId());
$wp->commit();

$editor_insert = $wp->prepare('
INSERT INTO pantheon_wp.wp_users
(user_nicename, user_login, user_registered, display_name, user_pass)
VALUES (
  "testeditor", # NICENAME lowercase, - instead of space
  "testeditor", # login lowercase, no spaces
  FROM_UNIXTIME("1970-1-1 00:00:00"),
  "",
  "$P$BdXwadO9LSW9siwU7iqcBWwyF1E/og/"
);
');
$wp->beginTransaction();
  $editor_insert->execute();
  $editor_id = $wp->lastInsertId();
  print($wp->lastInsertId());
  print "\n user id ^^ \n";
$wp->commit();


$roles_insert = $wp->prepare("
REPLACE INTO pantheon_wp.wp_usermeta
(meta_key, meta_value, user_id)
VALUES ( 
'wp_capabilities',
'a:1:{s:6:\"editor\";s:1:\"1\";}',
?
)
;
");
$wp->beginTransaction();
  $roles_insert->execute(Array($editor_id));
  print($wp->lastInsertId());
  print "\n user meta ^^ \n";
$wp->commit();

$wp->beginTransaction();
  $level_insert->execute(Array(7, $editor_id));
  print($wp->lastInsertId());
$wp->commit();
