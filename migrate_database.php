<?php
/**
  * Start files migrating
  * if we're using pantheon, I have a nice rsync bash script at pantheon_rsync.sh
  *
  * Set hostname, username, password for your local mysql install
  * Set drupal database name
  * set wp database name
  * Run this file: php migrate_database.php
  * this will take a while
  * Upload the database to wherever
  *
  * Once all the files are moved and the database is imported
  * open up the wp admin and start image regen plugin. 
  * This will take a while
  * open up zones and create zones for :
  *   top_stories
  *   homepage_featured
 **/
$hostname="localhost";  
$username="root";   
$password="p";  
$d6_db = "mjd6";  
$wp_db = "pantheon_wp";  
$FILEDIR = "http://dev-mjben.pantheonsite.io/wp-content/uploads/";


$d6 = new PDO("mysql:host=$hostname;dbname=$d6_db", $username, $password);  

$wp = new PDO("mysql:host=$hostname;dbname=$wp_db", $username, $password);  

//truncate term tables
$wp->beginTransaction();
$wp->exec('
TRUNCATE TABLE wp_terms;
TRUNCATE TABLE wp_term_taxonomy;
TRUNCATE pantheon_wp.wp_term_relationships;
');
$wp->commit();

$term_insert_data = $d6->prepare('
SELECT *
FROM term_data
;'
);
$term_insert_data->execute();

$term_insert = $wp->prepare('
INSERT INTO wp_terms
(term_id, `name`, slug, term_group)
VALUES (
?, ?, REPLACE(LOWER(?), " ", "-"), ?
)
;'
);
$term_insert->bindParam(1, $tid);
$term_insert->bindParam(2, $name);
$term_insert->bindParam(3, $name);
$term_insert->bindParam(4, $vid);

$wp->beginTransaction();
while ( $term = $term_insert_data->fetch(PDO::FETCH_ASSOC)) {
  if ($term['name'] === "photo_essays") {
    $term['name'] = "photoessays";
  }
	$tid = $term['tid'];
	$name = $term['name'];
	$vid = $term['vid'];
	$term_insert->execute();
}
$wp->commit();


$taxonomy_data = $d6->prepare('
SELECT DISTINCT
d.tid `term_id`,
d.vid `taxonomy`
FROM mjd6.term_data d
INNER JOIN mjd6.term_node n
USING(tid)
WHERE (1)
'
);
$taxonomy_data->execute();

$tax_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_term_taxonomy
(term_id, taxonomy, description, parent)
VALUES (
?,
?,
"",
0
)
;'
);
$tax_insert->bindParam(1, $tid);
$tax_insert->bindParam(2, $tax);

$term_to_tax_term = [];
$wp->beginTransaction();
while ( $row = $taxonomy_data->fetch(PDO::FETCH_ASSOC)) {
	$tid = $row['term_id'];
	$tax = $row['taxonomy'];
	switch ($tax) {
		case "9":
			$tax = "mj_primary_tag";
			break;
		case "2":
			$tax = "mj_blog_type";
			break;
		case "61":
			$tax = "mj_media_type";
			break;
		case "1":
			$tax = "category";
			break;
	}
	$tax_insert->execute();
  $term_to_tax_term[$row['term_id']] = $wp->lastInsertId();
}
$wp->commit();

// assign tags to articles
$term_rel_data = $d6->prepare("
SELECT DISTINCT nid, tid FROM mjd6.term_node
;
");
$term_rel_data->execute();

$term_rel_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_term_relationships 
(object_id, term_taxonomy_id)
VALUES (?, ?)
');

$wp->beginTransaction();
while ( $term = $term_rel_data->fetch(PDO::FETCH_NUM)) {
  $term[1] = $term_to_tax_term[$term[1]];
	$term_rel_insert->execute($term);
}
$wp->commit();

// Update tag counts.
$wp->beginTransaction();
$wp->exec('
UPDATE pantheon_wp.wp_term_taxonomy tt
SET `count` = (
SELECT COUNT(tr.object_id)
FROM pantheon_wp.wp_term_relationships tr
WHERE tr.term_taxonomy_id = tt.term_taxonomy_id
)
;
');
$wp->commit();

echo "taxonomy done";

$wp->beginTransaction();
$wp->exec('
TRUNCATE pantheon_wp.wp_posts;
TRUNCATE pantheon_wp.wp_postmeta;
');
$wp->commit();

$post_data = $d6->prepare("
SELECT DISTINCT
n.nid,
n.uid,
FROM_UNIXTIME(n.created),
FROM_UNIXTIME(n.created),
r.body,
n.title,
r.teaser,
IF( 
	LOCATE('/', a.dst),
	SUBSTR(a.dst, 
		CHAR_LENGTH(a.dst) - LOCATE('/', REVERSE(a.dst)) + 2 
	),
	a.dst
),
'',
'',
FROM_UNIXTIME(n.changed),
FROM_UNIXTIME(n.changed),
'',
n.type,
IF(n.status = 1, 'publish', 'private')
FROM mjd6.node n
INNER JOIN mjd6.node_revisions r
USING(vid)
LEFT OUTER JOIN mjd6.url_alias a
ON a.src = CONCAT('node/', n.nid)
WHERE n.type IN ('article', 'blogpost', 'page', 'full_width_article')
;
");
$post_data->execute();


$post_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_posts
(ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,
post_name, to_ping, pinged, post_modified, post_modified_gmt,
post_content_filtered, post_type, `post_status`)
VALUES (?, ?, ?, ?, ?, ?, ?,
?, ?, ?, ?, ?,
?, ?, ?)
');

$wp->beginTransaction();
while ( $post = $post_data->fetch(PDO::FETCH_NUM)) {
	$post_insert->execute($post);
}
$wp->commit();


$wp->beginTransaction();
$wp->exec('
UPDATE pantheon_wp.wp_posts
  SET post_type="mj_article"
  WHERE post_type="article";

UPDATE pantheon_wp.wp_posts
  SET post_type="mj_full_width"
  WHERE post_type="full_width_article";

UPDATE pantheon_wp.wp_posts
  SET post_type="mj_blog_post"
  WHERE post_type="blogpost";

UPDATE pantheon_wp.wp_posts
  SET post_author IS NULL
  WHERE post_author NOT IN (SELECT DISTINCT ID FROM pantheon_wp.wp_users) ;

');
$wp->commit();


//for blog body
$meta_data = $d6->prepare("
SELECT DISTINCT 
n.nid, 'body',
IF( 
  e.field_extended_body_value IS NULL,
  b.field_short_body_value,
  CONCAT(b.field_short_body_value, e.field_extended_body_value)
)
FROM mjd6.node n
INNER JOIN mjd6.content_field_short_body b
USING(vid)
INNER JOIN mjd6.content_type_blogpost e
USING(vid)
WHERE n.type='blogpost'
;
");
$meta_data->execute();

$meta_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_postmeta 
(post_id, meta_key, meta_value)
VALUES (?, ?, ?)
;
');
$wp->beginTransaction();
while ( $meta = $meta_data->fetch(PDO::FETCH_NUM)) {
	$meta_insert->execute($meta);
}
$wp->commit();

//for article bodys
$meta_data = $d6->prepare('
SELECT DISTINCT 
n.nid, "body", 
IF( 
  e.field_article_text_value IS NULL,
  b.field_short_body_value,
  CONCAT(b.field_short_body_value, e.field_article_text_value)
)
FROM mjd6.node n
INNER JOIN mjd6.content_field_short_body b
USING(vid)
INNER JOIN mjd6.content_field_article_text e
USING(vid)
WHERE n.type="article"
;
');
$meta_data->execute();

$wp->beginTransaction();
while ( $meta = $meta_data->fetch(PDO::FETCH_NUM)) {
	$meta_insert->execute($meta);
}
$wp->commit();


//for full width bodys
$meta_data = $d6->prepare('
SELECT DISTINCT 
n.nid, "body", b.field_short_body_value
FROM mjd6.node n
INNER JOIN mjd6.content_field_short_body b
USING(vid)
WHERE n.type="full_width_article"
;
');
$meta_data->execute();

$wp->beginTransaction();
while ( $meta = $meta_data->fetch(PDO::FETCH_NUM)) {
	$meta_insert->execute($meta);
}
$wp->commit();

//for dek
$meta_data = $d6->prepare('
SELECT DISTINCT 
n.nid, "dek", d.field_dek_value
FROM mjd6.node n
INNER JOIN mjd6.content_field_dek d
USING(vid)
;
');
$meta_data->execute();

$wp->beginTransaction();
while ( $meta = $meta_data->fetch(PDO::FETCH_NUM)) {
	$meta_insert->execute($meta);
}
$wp->commit();

//for dateline override
$meta_data = $d6->prepare('
SELECT DISTINCT n.nid, "dateline_override", d.field_issue_date_value
FROM mjd6.node n
INNER JOIN mjd6.content_field_issue_date d
USING(vid)
WHERE d.field_issue_date_value IS NOT NULL
;
');
$meta_data->execute();

$wp->beginTransaction();
while ( $meta = $meta_data->fetch(PDO::FETCH_NUM)) {
	$meta_insert->execute($meta);
}
$wp->commit();

//for byline override
$meta_data = $d6->prepare('
SELECT DISTINCT n.nid, "byline_override", b.field_issue_date_value
FROM mjd6.node n
INNER JOIN mjd6.field_byline_override_value b
USING(vid)
WHERE d.field_byline_override_value IS NOT NULL
;
');
$meta_data->execute();

$wp->beginTransaction();
while ( $meta = $meta_data->fetch(PDO::FETCH_NUM)) {
	$meta_insert->execute($meta);
}
$wp->commit();

//for social
$meta_data = $d6->prepare('
SELECT DISTINCT 
n.nid,
t.field_social_title_value,
d.field_social_dek_value
FROM mjd6.node n
INNER JOIN mjd6.content_field_social_dek d
USING(vid)
INNER JOIN mjd6.content_field_social_title t
USING(vid)
;
');
$meta_data->execute();

$wp->beginTransaction();
while ( $meta = $meta_data->fetch(PDO::FETCH_ASSOC)) {
  $social_value = serialize( array(
    'social_title' => $meta['field_social_title_value'],
    'social_dek' => $meta['field_social_dek_value'],
    'standout' => false,
    'fb_instant_exclude' => false,
  ) );
	$meta_insert->execute(array($meta['nid'], 'social', $social_value) );
}
$wp->commit();

//for alt
$meta_data = $d6->prepare('
SELECT DISTINCT
n.nid,
t.field_alternate_title_value,
d.field_alternate_dek_value
FROM mjd6.node n
INNER JOIN mjd6.content_field_alternate_dek d
USING(vid)
INNER JOIN mjd6.content_field_alternate_title t
USING(vid)
;
');
$meta_data->execute();

$wp->beginTransaction();
while ( $meta = $meta_data->fetch(PDO::FETCH_ASSOC)) {
  $alt_value = serialize( array(
    'alt_title' => $meta['field_alternate_title_value'],
    'alt_dek' => $meta['field_alternate_dek_value'],
  ) );
	$meta_insert->execute(array($meta['nid'], 'alt', $alt_value) );
}
$wp->commit();

//for css, js
$meta_data = $d6->prepare('
SELECT DISTINCT
n.nid,
c.field_css_value,
j.field_javascript_value
FROM mjd6.node n
INNER JOIN mjd6.content_field_css c
USING(vid)
INNER JOIN mjd6.content_field_javascript j
USING(vid)
;
');
$meta_data->execute();

$wp->beginTransaction();
while ( $meta = $meta_data->fetch(PDO::FETCH_ASSOC)) {
  $cssjs_value = serialize( array(
    'css' => $meta['field_css_value'],
    'js' => $meta['field_javascript_value'],
  ) );
	$meta_insert->execute(array($meta['nid'], 'css_js', $cssjs_value) );
}
$wp->commit();

//for relateds
$meta_data = $d6->prepare('
SELECT DISTINCT n.nid,
GROUP_CONCAT(
  DISTINCT r.field_related_articles_nid 
  SEPARATOR ","
) `relateds`
FROM mjd6.node n
INNER JOIN mjd6.content_field_related_articles r
USING(vid)
GROUP BY n.nid
');
$meta_data->execute();

$wp->beginTransaction();
while ( $meta = $meta_data->fetch(PDO::FETCH_ASSOC)) {
  $related_value = serialize( array(
    'relateds' => explode(',', $meta['relateds'])
  ) );
  
	$meta_insert->execute(array($meta['nid'], 'related_articles', $related_value) );
}
$wp->commit();

echo "posts done";


$wp->beginTransaction();
$wp->exec('
DELETE FROM pantheon_wp.wp_users WHERE ID > 1;
DELETE FROM pantheon_wp.wp_usermeta WHERE user_id > 1;
');
$wp->commit();

$author_data = $d6->prepare('
SELECT DISTINCT
u.uid,
u.mail,
NULL,
u.name,
u.mail,
FROM_UNIXTIME(created),
"",
0,
u.name
FROM mjd6.users u
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

//everybody is a contributor! fuck FIXME
$user_data = $d6->prepare("
SELECT DISTINCT
u.uid, 'wp_capabilities', 'a:1:{s:13:\"former_author\";s:1:\"1\";}'
FROM mjd6.users u
;"
);
$author_data->execute();

$user_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_usermeta (user_id, meta_key, meta_value)
VALUES ( ?, ?, ? )
;
");

$wp->beginTransaction();
while ( $user = $user_data->fetch(PDO::FETCH_NUM)) {
	$user_insert->execute($role);
}
$wp->commit();


//author roles who are active users
$roles_data = $d6->prepare("
SELECT DISTINCT
u.uid
FROM mjd6.users u
INNER JOIN mjd6.users_roles r
USING (uid)
;
");
$roles_data->execute();

$roles_insert = $wp->prepare("
UPDATE pantheon_wp.wp_usermeta 
SET meta_value = 'a:1:{s:6:\"author\";s:1:\"1\";}'
WHERE meta_key = 'wp_capabilities'
AND user_id = ?
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
//THIS SEEMS TO GET THE RIGHT THING

$byline_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_term_relationships 
VALUES(?, ?, 0)
;
"); //node is 254151, uid is 83242
$wp->beginTransaction();
while ( $byline = $byline_data->fetch(PDO::FETCH_NUM)) {
	$byline_insert->execute($byline);
}
$wp->commit();

echo "authors done";

$hostname="localhost";  
$username="root";   
$password="p";  

$d6_db = "mjd6";  
$d6 = new PDO("mysql:host=$hostname;dbname=$d6_db", $username, $password);  

$wp_db = "pantheon_wp";  
$wp = new PDO("mysql:host=$hostname;dbname=$wp_db", $username, $password);  
$wp->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

//FIXME REPEAT FOR FULL WIDTH TITLES< content_field_top_of_content_image 
//for master images
$master_data = $d6->prepare('
SELECT DISTINCT 
n.nid,
n.uid,
n.created,
n.changed,
n.status,
i.field_master_image_data,
c.field_master_image_caption_value,
b.field_art_byline_value,
s.field_suppress_master_image_value,
f.filemime,
f.filename
FROM mjd6.node n
INNER JOIN mjd6.content_field_master_image i
USING(vid)
INNER JOIN mjd6.content_field_master_image_caption c
USING(vid)
INNER JOIN mjd6.content_field_art_byline b
USING(vid)
INNER JOIN mjd6.content_field_suppress_master_image s
USING(vid)
INNER JOIN mjd6.files f
ON(i.field_master_image_fid = f.fid)
;
');
$master_data->execute();

$master_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_posts
(post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,
post_name, to_ping, pinged, post_modified, post_modified_gmt, guid,
post_content_filtered, post_type, `post_status`, post_parent, post_mime_type)
VALUES (
:post_author,
FROM_UNIXTIME(:post_date),
FROM_UNIXTIME(:post_date),
"",
:post_title,
"",
:post_name,
"",
"",
FROM_UNIXTIME(:post_modified),
FROM_UNIXTIME(:post_modified),
:guid,
"",
"attachment",
IF(:status = 1, "publish", "private"),
:post_parent,
:post_mime_type
)
;
');

$master_meta_rows = array();

$wp->beginTransaction();
while ( $master = $master_data->fetch(PDO::FETCH_ASSOC)) {
  if (!$master['field_master_image_data']) { continue; }

  $master_data_array = unserialize($master['field_master_image_data']);

  $guid = $FILEDIR . $master['filename'];
  $post_name = preg_replace("/\.[^.]+$/", "", $master['filename'] );
  $post_title = $master_data_array['title'] 
    ? $master_data_array['title']
    : $post_name
  ;


  $master_insert->execute(array(
    ':post_author' => $master['uid'],
    ':post_date' => $master['created'],
    ':post_title' => $post_title,
    ':post_name' => $post_name,
    ':post_modified' => $master['changed'],
    ':guid' => $guid,
    ':status' => $master['status'],
    ':post_parent' => $master['nid'],
    ':post_mime_type' => $master['filemime'],
  ) );


  $master_meta_value = serialize( array(
    'master_image' => $wp->lastInsertId(),
    'master_image_byline' => $master['field_art_byline_value'],
    'master_image_caption' => $master['field_master_image_caption_value'],
    'master_image_suppress' => $master['field_suppress_master_image_value']
  ) );

  $master_meta_rows[] = array(
    'nid' => $master['nid'],
    'image_id' => $wp->lastInsertId(),
    'value' => $master_meta_value,
    'filename' => $master['filename']
  );
}
$wp->commit();



$master_meta_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_postmeta
(post_id, meta_key, meta_value)
VALUES (?, ?, ?)
;
");
$wp->beginTransaction();
foreach ( $master_meta_rows as $row ) {
  $master_meta_insert->execute(array(
    $row['nid'],
    'master_image',
    $row['value']
  ) );

  $master_meta_insert->execute(array(
    $row['image_id'],
    '_wp_attached_file',
    $row['filename']
  ) );
}
$wp->commit();

//TITLE IMAGES HERE
$title_data = $d6->prepare('
SELECT DISTINCT 
n.nid,
n.uid,
n.created,
n.changed,
n.status,
i.field_title_image_data,
i.field_title_image_credit_value,
f.filemime,
f.filename
FROM mjd6.node n
INNER JOIN mjd6.content_type_full_width_article i
USING(vid)
INNER JOIN mjd6.files f
ON(i.field_title_image_fid = f.fid)
;
');
$title_data->execute();

$title_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_posts
(post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,
post_name, to_ping, pinged, post_modified, post_modified_gmt, guid,
post_content_filtered, post_type, `post_status`, post_parent, post_mime_type)
VALUES (
:post_author,
FROM_UNIXTIME(:post_date),
FROM_UNIXTIME(:post_date),
"",
:post_title,
"",
:post_name,
"",
"",
FROM_UNIXTIME(:post_modified),
FROM_UNIXTIME(:post_modified),
:guid,
"",
"attachment",
IF(:status = 1, "publish", "private"),
:post_parent,
:post_mime_type
)
;
');

$title_meta_rows = array();

$wp->beginTransaction();
while ( $title = $title_data->fetch(PDO::FETCH_ASSOC)) {
  if (!$title['field_title_image_data']) { continue; }

  $title_data_array = unserialize($title['field_title_image_data']);

  $guid = $FILEDIR . $title['filename'];
  $post_name = preg_replace("/\.[^.]+$/", "", $title['filename'] );
  $post_title = $title_data_array['title'] 
    ? $title_data_array['title']
    : $post_name
  ;


  $title_insert->execute(array(
    ':post_author' => $title['uid'],
    ':post_date' => $title['created'],
    ':post_title' => $post_title,
    ':post_name' => $post_name,
    ':post_modified' => $title['changed'],
    ':guid' => $guid,
    ':status' => $title['status'],
    ':post_parent' => $title['nid'],
    ':post_mime_type' => $title['filemime'],
  ) );


  $title_meta_value = serialize( array(
    'title_image' => $wp->lastInsertId(),
    'title_image_credit' => $title['field_title_image_credit_value'],
  ) );

  $title_meta_rows[] = array(
    'nid' => $title['nid'],
    'image_id' => $wp->lastInsertId(),
    'value' => $title_meta_value,
    'filename' => $master['filename']
  );
}
$wp->commit();


$title_meta_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_postmeta
(post_id, meta_key, meta_value)
VALUES (?, ?, ?)
;
");
$wp->beginTransaction();
foreach ( $title_meta_rows as $row ) {
  $title_meta_insert->execute(array(
    $row['nid'],
    'title_image',
    $row['value']
  ) );

  $title_meta_insert->execute(array(
    $row['image_id'],
    '_wp_attached_file',
    $row['filename']
  ) );
}
$wp->commit();

echo "images done";

$file_data = $d6->prepare('
SELECT DISTINCT
f.uid,
u.nid,
n.created,
n.changed,
n.status,
f.filemime,
f.filename,
f.fid
FROM mjd6.upload u
INNER JOIN mjd6.files f
USING(fid)
INNER JOIN mjd6.node n
ON(u.nid = n.nid)
;
');
$file_data->execute();

$file_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_posts
(post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,
post_name, to_ping, pinged, post_modified, post_modified_gmt, guid,
post_content_filtered, post_type, `post_status`, post_parent, post_mime_type)
VALUES (
:post_author,
FROM_UNIXTIME(:post_date),
FROM_UNIXTIME(:post_date),
"",
:post_title,
"",
:post_name,
"",
"",
FROM_UNIXTIME(:post_modified),
FROM_UNIXTIME(:post_modified),
:guid,
"",
"attachment",
IF(:status = 1, "publish", "private"),
:post_parent,
:post_mime_type
)
;
');

$file_meta_rows = array();
$node_file_rows = array();

$wp->beginTransaction();
while ( $file = $file_data->fetch(PDO::FETCH_ASSOC)) {

  $guid = $FILEDIR . $file['filename'];
  $post_name = preg_replace("/\.[^.]+$/", "", $file['filename'] );

  $file_insert->execute(array(
    ':post_author' => $file['uid'],
    ':post_date' => $file['created'],
    ':post_title' => $post_name,
    ':post_name' => $post_name,
    ':post_modified' => $file['changed'],
    ':guid' => $guid,
    ':status' => $file['status'],
    ':post_parent' => $file['nid'],
    ':post_mime_type' => $file['filemime'],
  ) );


  $file_meta_rows[] = array(
    'nid' => $file['nid'],
    'fid' => $wp->lastInsertId(),
    'filename' => $file['filename']
  );

  $node_file_rows[$file]['nid'] 
    ?  $node_file_rows[$file]['nid'] 
    :  array();
  $node_file_rows[$file]['nid'][] = $wp->lastInsertId(); 

}
$wp->commit();


$file_meta_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_postmeta
(post_id, meta_key, meta_value)
VALUES (?, ?, ?)
;
");
$wp->beginTransaction();
foreach ( $file_meta_rows as $row ) {

  $file_meta_insert->execute(array(
    $row['fid'],
    '_wp_attached_file',
    $row['filename']
  ) );
}

foreach ( $node_file_rows as $nid ) {
  $file_meta_insert->execute(array(
    $nid,
    'file_attachments',
    serialize($node_file_rows[$nid])
  ) );
}

$wp->commit();

echo "files done";

?>
