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

$wp->beginTransaction();
$post_insert = $wp->exec('
INSERT IGNORE INTO pantheon_wp.wp_posts
(post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,
post_name, to_ping, pinged, post_modified, post_modified_gmt,
post_content_filtered, post_type, `post_status`, `post_parent`)
VALUES (
1, 
FROM_UNIXTIME("2017-03-31 16:21:39"),
FROM_UNIXTIME("2017-03-31 16:21:39"),
"", "Podcasts", "",
"podcasts", "", "",
FROM_UNIXTIME("2017-03-31 16:21:39"),
FROM_UNIXTIME("2017-03-31 16:21:39"),
"", "page", "publish", 0)
;
');
$wp->commit();
