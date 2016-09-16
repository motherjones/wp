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
?>
FIXME
# file_attachments: need to pull one in to see structure
