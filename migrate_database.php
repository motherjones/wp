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
$password=$argv[1];
$d6_db = "mjd6";  
$wp_db = "pantheon_wp";  
$FILEDIR = "http://dev-mjwordpress.pantheonsite.io/wp-content/uploads/";


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

$article_term_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_terms
(name, slug)
VALUES (
?,
CONCAT( "cap-", REPLACE(LOWER(?), " ", "-") ),
)
;'
);

$article_types = Array('article', 'blogpost', 'full_width_article');
$article_type_terms = Array();
$wp->beginTransaction();
foreach ( $article_types as $type) {
  $article_term_insert->execute(Array($type, $type));
  $article_type_terms[$type] = $wp->lastInsertId();
}
$wp->commit();

$tax_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_term_taxonomy
(term_id, taxonomy, description, parent)
VALUES (
?
"mj_article_type",
"",
0
)
;'
);
$wp_tax_id = Array();
$wp->beginTransaction();
foreach ($article_type_terms as $type => $term_id) {
  $tax_insert->execute(Array($term_id));
  $wp_tax_id[$type] = $wp->lastInsertId();
}
$wp->commit();


$term_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_term_relationships 
(object_id, term_taxonomy_id)
VALUES (
SELECT p.ID, tax.term_taxonomy_id
FROM wp_posts p
JOIN
wp_terms term
ON (p.post_type = term.slug)
JOIN
wp_term_taxonomy tax
ON (tax.term_id = term.term_id)
)
;'
);

$wp->beginTransaction();
$wp->exec('
UPDATE pantheon_wp.wp_posts
  SET post_type="post"
  WHERE post_type="article";

UPDATE pantheon_wp.wp_posts
  SET post_type="post"
  WHERE post_type="full_width_article";

UPDATE pantheon_wp.wp_posts
  SET post_type="post"
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

$user_data = $d6->prepare('
SELECT
DISTINCT
u.uid,
u.mail,
NULL,
n.title,
u.mail,
FROM_UNIXTIME(u.created),
"",
0,
n.title
FROM
mjd6.content_type_author a
INNER JOIN mjd6.node n
ON (a.nid = n.nid)
LEFT JOIN mjd6.users u
ON (u.name = n.title)
;'
);
$user_data->execute();

$user_insert = $wp->prepare('
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

$author_hash = Array();

$wp->beginTransaction();
while ( $user = $user_data->fetch(PDO::FETCH_NUM)) {
	$user_insert->execute($user);
  // example:    description: ben   ben 1 bbreedlove@motherjones.com
  $description = $user[3] . '   ' . $user[3]
    . ' ' . $wp->lastInsertId() . ' ' . $user[1];
  $author_hash[$user[3]] = Array(
    'wp_user_id' => $wp->lastInsertId(),
    'description' => $description,
    'email' => $user[1],
  ); 
}
$wp->commit();

//naming for co authors taxonomy is cap-username
$byline_titles_data = $d6->prepare("
SELECT DISTINCT
n.title
FROM mjd6.content_field_byline b
INNER JOIN mjd6.node n
ON (n.nid = b.field_byline_nid)
;"
);
$byline_titles_data->execute();

$byline_titles_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_terms
(name, slug, term_group)
VALUES (
?,
CONCAT( "cap-", REPLACE(LOWER(?), " ", "-") ),
0
)
;'
);

$wp->beginTransaction();
while ( $byline = $byline_titles_data->fetch(PDO::FETCH_ASSOC)) {
  $byline_titles_insert->execute(Array(
    $byline['title'],
    $byline['title']
  ));
  if (array_key_exists($byline['title'], $author_hash)) {
    $author_hash[$byline['title']]['term_id'] = $wp->lastInsertId();
  } else {
    $description = $byline['title'] . '   ' . $byline['title']
      . ' ' . $wp->lastInsertId() . ' ';
    $author_hash[$byline['title']] = Array(
      'term_id' => $wp->lastInsertId(),
      'description' => $description
    ); 
  }
}
$wp->commit();


//CREATE GUEST AUTHORS FOR ALL USERS

$author_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_posts
(post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,
post_name, to_ping, pinged, post_modified, post_modified_gmt,
post_content_filtered, post_type, `post_status`)
VALUES (
  1,
  FROM_UNIXTIME(?), #POST DATE
  FROM_UNIXTIME(?), ##POST DATE GMT
  "", ##post content
  ?, ##post title is author name for display
  "",  #post excerpt
  CONCAT( "cat-", REPLACE(LOWER(?), " ", "-") ),# post name is cap-first-last
  "", #to ping
  "", # pinged
  FROM_UNIXTIME(?), #post_modified
  FROM_UNIXTIME(?), #post_modified_GGMT
  "", #post content filtered
  "guest-author", #post type
  "publish" # post status
)
');

$wp->beginTransaction();
foreach ( $author_hash as $author => $author_array ) {
  $author_insert->execute(Array(
    '1970-1-1 00:00:00', //post date
    '1970-1-1 00:00:00', //post date gmt
    $author, //title
    $author, //name
    '1970-1-1 00:00:00', //post modified
    '1970-1-1 00:00:00', //post modified gmt
  ));
  $author_array['post_id'] = $wp->lastInsertId();
}
$wp->commit();


$byline_taxonomy_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_term_taxonomy
(term_id, taxonomy, description)
VALUES (
?,
'author',
?)
;
");
$wp->beginTransaction();
foreach ( $author_hash as $author => $author_array ) {
  $byline_taxonomy_insert->execute(Array(
    $author_array['term_id'],
    $author_array['description']
  ));
  $author_hash[$author]['tax_id'] = $wp->lastInsertId();
}
$wp->commit();

$byline_term_data = $d6->prepare("
SELECT DISTINCT
n.nid,
b.nid `node`,
n.title `title`
FROM mjd6.content_field_byline b
INNER JOIN mjd6.node n
ON (n.nid = b.field_byline_nid)
;"
);
$byline_term_data->execute();

$byline_term_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_term_relationships 
(object_id, term_taxonomy_id)
VALUES (?, ?)
;
");

$wp->beginTransaction();
while ( $term = $byline_term_data->fetch(PDO::FETCH_ASSOC)) {
  if (array_key_exists($term['title'], $author_hash)) {
    $byline_term_insert->execute(Array(
      $term['node'],
      $author_hash[$term['title']]['tax_id']
    ));
  }
}
$wp->commit();


//everybody is a contributor! Later we can make active users active
//
$wp->beginTransaction();
$user_insert = $wp->exec("
INSERT IGNORE INTO pantheon_wp.wp_usermeta (user_id, meta_key, meta_value)
SELECT DISTINCT
ID, 'wp_capabilities', 'a:1:{s:11:\"contributor\";s:1:\"1\";}'
FROM pantheon_wp.wp_users
;
");
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
a.field_photo_fid,
a.title,
u.name
FROM mjd6.node n
INNER JOIN mjd6.content_type_author a 
ON a.nid = n.nid
LEFT JOIN mjd6.users u
ON u.uid=a.field_user_uid
WHERE name IS NOT NULL
;
");
$author_meta_data->execute();

$author_meta_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_postmeta (post_id, meta_key, meta_value)
VALUES ( ?, ?, ? )
;
");
$author_meta_insert->bindParam(1, $pud);
$author_meta_insert->bindParam(2, $key);
$author_meta_insert->bindParam(3, $value);
$wp->beginTransaction();
while ( $author_meta = $author_meta_data->fetch(PDO::FETCH_ASSOC)) {
  $author_array = $author_hash[$author_meta['name']];

  if (!$author_array) { next; }
  $pid = $author_array['post_id'];

  if ($author_array['wp_user_id']) {
    //Yeah. We saved this from all the way up there. Jesus
    $key = "cap-linked_account";
    $value = str_replace(' ', '-', strtolower($author_meta['title']) );
    $author_meta_insert->execute();

    $key = "cap-user_email";
    $value = $author_array['email'];
    $author_meta_insert->execute();
  }

  $key = "cap-twitter";
  $value = $author_meta['field_twitter_user_value'];
	$author_meta_insert->execute();

  $key = "cap-last_name";
  $value = $author_meta['field_last_name_value'];
	$author_meta_insert->execute();

  $key = "cap-long_bio";
  $value = $author_meta['field_author_bio_value'];
	$author_meta_insert->execute();

  $key = "cap-position";
  $value = $author_meta['field_author_title_value'];
	$author_meta_insert->execute();

  $key = "cap-short_bio";
  $value = $author_meta['field_author_bio_short_value'];
	$author_meta_insert->execute();

  $key = "cap-display_name";
  $value = $author_meta['title'];
	$author_meta_insert->execute();

  $key = "cap-user_login";
  $value = str_replace( ' ', '-', strtolower($author_meta['name']) );
	$author_meta_insert->execute();

}
$wp->commit();

/*** FIXXXMEEE
 * now authors are posts so follow master image attachement style
 */
//author photo
$author_image_data = $d6->prepare("
SELECT DISTINCT
n.nid,
#u.uid,
#u.created,
a.field_user_uid, 
f.status,
f.filemime,
f.filepath,
f.fid,
a.field_author_title_value,
a.field_photo_fid
FROM mjd6.node n
INNER JOIN mjd6.node_revisions r
USING(vid)
LEFT OUTER JOIN mjd6.content_type_author a 
USING(vid)
#LEFT OUTER JOIN mjd6.users u
#ON u.uid=a.field_user_uid
INNER JOIN mjd6.files f
ON(a.field_photo_fid = f.fid)
#WHERE name IS NOT NULL
;
");
$author_image_data->execute();

$author_image_insert = $wp->prepare('
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

$author_fid = Array();
$wp->beginTransaction();
while ( $author_image = $author_image_data->fetch(PDO::FETCH_ASSOC) ) {

  $guid = $FILEDIR . preg_replace('/files\//', '', $author_image['filepath']);
  $post_name = preg_replace("/\.[^.]+$/", "", $author_image['filepath'] );
  $post_name = preg_replace("/files\//", "", $author_image['filepath'] );

  $post_id = $author_hash[$author_image['title']]['post_id'];

  $author_image_insert->execute(array(
    ':post_author' => 1,
    ':post_date' => '1970-1-1 00:00:00', //post date
    ':post_title' => $post_name,
    ':post_name' => $post_name,
    ':post_modified' => '1970-1-1 00:00:00', //post modified
    ':guid' => $guid,
    ':status' => $author_image['status'],
    ':post_parent' => $post_id,
    ':post_mime_type' => $author_image['filemime'],
  ) );
  $author_fid[] = Array( // Set author to have the photo
    $post_id,
    '_thumbnail_id',
    $wp->lastInsertId()
  );
  $author_fid[] = Array( // Set photo to have a filepath
    $wp->lastInsertId(),
    '_wp_attached_file',
    $guid
  );
  //FIXME we're not doing _wp_attachment_metadata
  //god i hope we don't need it because jesus
}
$wp->commit();

$author_image_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_postmeta (post_id, meta_key, meta_value)
VALUES ( ?, ?, ? )
;
");
$wp->beginTransaction();
foreach ($author_fid as $fid ) {
	$author_image_insert->execute($fid);
}
$wp->commit();



echo "authors done";

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

  $node_file_rows[$file['nid']] 
    = in_array($file['nid'], $node_file_rows, TRUE)
      ?  $node_file_rows[$file['nid']] 
      :  Array();
  $node_file_rows[$file['nid']][] = $wp->lastInsertId(); 

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

foreach ( $node_file_rows as $nid => $row ) {
  $file_meta_insert->execute(array(
    $nid,
    'file_attachments',
    serialize($row)
  ) );
}

$wp->commit();

echo "files done";

?>
