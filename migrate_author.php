<?php
$hostname="localhost";  
$username="root";   
$password="p";  

$d6_db = "mjd6";  
$d6 = new PDO("mysql:host=$hostname;dbname=$d6_db", $username, $password);  

$wp_db = "pantheon_wp";  
$wp = new PDO("mysql:host=$hostname;dbname=$wp_db", $username, $password);  

$wp->beginTransaction();
$wp->exec('
DELETE FROM pantheon_wp.wp_users WHERE ID > 1;
DELETE FROM pantheon_wp.wp_usermeta WHERE user_id > 1;
');
$wp->commit();

$author_data = $d6->prepare('
SELECT DISTINCT
u.uid, u.mail, NULL, u.name, u.mail,
FROM_UNIXTIME(created), "", 0, u.name
FROM mjd6.users u
INNER JOIN mjd6.users_roles r
USING (uid)
;'
);
$author_data->execute();

$author_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_users
(ID, user_login, user_pass, user_nicename, user_email,
user_registered, user_activation_key, user_status, display_name)
VALUES (
?,
?,
?,
REPLACE(LOWER(?), " ", "-"),
?,
?,
?,
?,
?
)
;'
);
$wp->beginTransaction();
while ( $author = $author_data->fetch(PDO::FETCH_NUM)) {
	$author_insert->execute($author);
}
$wp->commit();

//author roles
$roles_data = $d6->prepare("
SELECT DISTINCT
u.uid, 'wp_capabilities', 'a:1:{s:6:\"author\";s:1:\"1\";}'
FROM mjd6.users u
INNER JOIN mjd6.users_roles r
USING (uid)
;
");
$roles_data->execute();

$roles_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_usermeta (user_id, meta_key, meta_value)
VALUES ( ?, ?, ? )
;
");
$wp->beginTransaction();
while ( $role = $roles_data->fetch(PDO::FETCH_NUM)) {
	$roles_insert->execute($role);
}
$wp->commit();

$wp->beginTransaction();
$wp->exec("
UPDATE pantheon_wp.wp_usermeta
SET meta_value = 'a:1:{s:13:\"administrator\";s:1:\"1\";}'
WHERE user_id IN (1) AND meta_key = 'wp_capabilities'
;
UPDATE pantheon_wp.wp_usermeta
SET meta_value = '10'
WHERE user_id IN (1) AND meta_key = 'wp_user_level'
;

UPDATE pantheon_wp.wp_posts
SET post_author = NULL
WHERE post_author NOT IN (SELECT DISTINCT ID FROM pantheon_wp.wp_users)
;
");
$wp->commit();


$author_meta_data = $d6->prepare("
SELECT DISTINCT
n.nid,
u.uid,
a.field_user_uid, 
a.field_twitter_user_value,
a.field_last_name_value,
a.field_author_bio_short_value,
a.field_author_title_value,
a.field_author_bio_value,
u.name
FROM mjd6.node n
INNER JOIN mjd6.node_revisions r
USING(vid)
LEFT OUTER JOIN mjd6.content_type_author a 
USING(vid)
LEFT OUTER JOIN mjd6.users u
ON u.uid=a.field_user_uid
WHERE name IS NOT NULL
;
");
$author_meta_data->execute();

$author_meta_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_usermeta (user_id, meta_key, meta_value)
VALUES ( ?, ?, ? )
;
");
$author_meta_insert->bindParam(1, $uid);
$author_meta_insert->bindParam(2, $key);
$author_meta_insert->bindParam(3, $value);
$wp->beginTransaction();
while ( $author_meta = $author_meta_data->fetch(PDO::FETCH_ASSOC)) {
  $uid = $author_meta['uid'];

  $key = "twitter";
  $value = $author_meta['field_twitter_user_value'];
	$author_meta_insert->execute();

  $key = "last_name";
  $value = $author_meta['field_last_name_value'];
	$author_meta_insert->execute();

  $key = "long_bio";
  $value = $author_meta['field_author_bio_value'];
	$author_meta_insert->execute();

  $key = "position";
  $value = $author_meta['field_author_title_value'];
	$author_meta_insert->execute();

  $key = "short_bio";
  $value = $author_meta['field_author_bio_short_value'];
	$author_meta_insert->execute();

  $key = "nickname";
  $value = $author_meta['name'];
	$author_meta_insert->execute();

}
$wp->commit();

//author byline
$byline_data = $d6->prepare("
SELECT DISTINCT n.nid, u.uid 
FROM mjd6.term_node n
INNER JOIN mjd6.content_field_byline b
ON n.nid = b.nid
INNER JOIN mjd6.content_type_author a
ON b.field_byline_nid=a.nid
INNER JOIN mjd6.users u
ON a.field_user_uid=u.uid
;
");
$byline_data->execute();

$byline_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_term_relationships 
VALUES(?, ?)
;
");
$wp->beginTransaction();
while ( $byline = $byline_data->fetch(PDO::FETCH_NUM)) {
	$byline_insert->execute($byline);
}
$wp->commit();

//FIXME images needed
?>
