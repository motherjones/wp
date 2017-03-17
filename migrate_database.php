<?php

$hostname="localhost";  
$username="root";   
$password=$argv[1];
$d6_db = "mjd6";  
$wp_db = "pantheon_wp";  
$FILEDIR_ABS = "http://dev-mjwordpress.pantheonsite.io/wp-content/uploads/";
$FILEDIR = "/wp-content/uploads/";


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
WHERE (vid = 9 OR vid = 2 OR vid = 1 
    OR tid = 22221 OR tid = 23631 OR tid = 22491) 
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
WHERE (d.vid = 9 OR d.vid = 2 OR d.vid = 1 
    OR d.tid = 22221 OR d.tid = 23631 OR d.tid = 22491) 
;
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
			$tax = "post_tag";
			break;
		case "2":
			$tax = "mj_blog_type";
			break;
		case "61": //media type
      if ($tid === "22221") { //is photoessay
        $tax = "post_tag";
        break;
      }
      continue 2;
		case "1":
			$tax = "category";
			break;
    case "5": //secondary tag
      if ($tid === "23631" || $tid === "22491") { //bite or inquiring minds
        $tax = "post_tag";
        break;
      }
      continue 2;
    default:
      continue 2;
	}
	$tax_insert->execute();
  $term_to_tax_term[$row['term_id']] = $wp->lastInsertId();
}
$wp->commit();

// assign tags to articles
$term_rel_data = $d6->prepare("
SELECT DISTINCT n.nid, n.tid FROM mjd6.term_node n JOIN mjd6.term_data d
ON n.tid = d.tid
WHERE (d.vid = 9 OR d.vid = 2 OR d.vid = 1 
    OR d.tid = 22221 OR d.tid = 23631 OR d.tid = 22491) 
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
IF(n.status = 1, 'publish', 'draft'),
0
FROM mjd6.node n
INNER JOIN mjd6.node_revisions r
USING(vid)
LEFT OUTER JOIN mjd6.url_alias a
ON a.src = CONCAT('node/', n.nid)
WHERE n.type IN ('article', 'blogpost', 'full_width_article')
;
");
$post_data->execute();


$post_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_posts
(ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,
post_name, to_ping, pinged, post_modified, post_modified_gmt,
post_content_filtered, post_type, `post_status`, `post_parent`)
VALUES (?, ?, ?, ?, ?, ?, ?,
?, ?, ?, ?, ?,
?, ?, ?, ?)
');

$wp->beginTransaction();
while ( $post = $post_data->fetch(PDO::FETCH_NUM)) {
	$post_insert->execute($post);
}
$wp->commit();


$about_data = $d6->prepare("
SELECT DISTINCT
n.nid,
n.uid,
FROM_UNIXTIME(n.created),
FROM_UNIXTIME(n.created),
r.body,
n.title,
r.teaser,
'about'
,
'',
'',
FROM_UNIXTIME(n.changed),
FROM_UNIXTIME(n.changed),
'',
n.type,
IF(n.status = 1, 'publish', 'draft'),
0
FROM mjd6.node n
INNER JOIN mjd6.node_revisions r
USING(vid)
LEFT OUTER JOIN mjd6.url_alias a
ON a.src = CONCAT('node/', n.nid)
WHERE n.nid = 64
;
");
$about_data->execute();

$wp->beginTransaction();
while ( $post = $about_data->fetch(PDO::FETCH_NUM)) {
	$post_insert->execute($post);
}
$wp->commit();

$page_data = $d6->prepare("
SELECT DISTINCT
n.nid,
n.uid,
FROM_UNIXTIME(n.created),
FROM_UNIXTIME(n.created),
r.body,
n.title,
r.teaser,
SUBSTR(a.dst, 
  CHAR_LENGTH(a.dst) - LOCATE('about/', REVERSE(a.dst)) + 2 
)
,
'',
'',
FROM_UNIXTIME(n.changed),
FROM_UNIXTIME(n.changed),
'',
n.type,
IF(n.status = 1, 'publish', 'draft'),
64
FROM mjd6.node n
INNER JOIN mjd6.node_revisions r
USING(vid)
LEFT OUTER JOIN mjd6.url_alias a
ON a.src = CONCAT('node/', n.nid)
WHERE n.type = 'page'
AND a.dst LIKE '%about%'
AND n.nid IS NOT 64
;
");
$page_data->execute();

$wp->beginTransaction();
while ( $post = $page_data->fetch(PDO::FETCH_NUM)) {
	$post_insert->execute($post);
}
$wp->commit();


$page_data = $d6->prepare("
SELECT DISTINCT
n.nid,
n.uid,
FROM_UNIXTIME(n.created),
FROM_UNIXTIME(n.created),
r.body,
n.title,
r.teaser,
REPLACE(
  SUBSTR(a.dst, 
    LOCATE('/', a.dst) + 1
  ), 
  \"/\",
  \"-\"
)
,
'',
'',
FROM_UNIXTIME(n.changed),
FROM_UNIXTIME(n.changed),
'',
n.type,
IF(n.status = 1, 'publish', 'draft'),
0
FROM mjd6.node n
INNER JOIN mjd6.node_revisions r
USING(vid)
LEFT OUTER JOIN mjd6.url_alias a
ON a.src = CONCAT('node/', n.nid)
WHERE n.type = 'page'
AND a.dst NOT LIKE '%about%'
AND a.dst NOT LIKE 'toc%'
;
");
$page_data->execute();

$wp->beginTransaction();
while ( $post = $page_data->fetch(PDO::FETCH_NUM)) {
	$post_insert->execute($post);
}
$wp->commit();

// toc call
$page_data = $d6->prepare("
SELECT DISTINCT
r.nid,
r.uid,
FROM_UNIXTIME(n.created),
r.body,
r.title,
r.teaser,
a.dst,
FROM_UNIXTIME(n.changed),
n.type,
IF(n.status = 1, 'publish', 'draft')
FROM mjd6.node n
INNER JOIN mjd6.node_revisions r
USING(vid)
LEFT OUTER JOIN mjd6.url_alias a
ON a.src = CONCAT('node/', n.nid)
WHERE (n.type = 'page' OR n.type = 'toc')
AND a.dst LIKE 'toc%'
;
");
$page_data->execute();

$toc_pages = Array();
$toc_year_pages = Array();
$toc_month_pages = Array();
$toc_magazine_pages = Array();

$redirects_needed = Array();

while ( $page = $page_data->fetch(PDO::FETCH_ASSOC)) {
  // form is toc/YYYY/MM/slug
  $path = preg_split('/\//', $page['dst']);
  $page['url_split'] = $path;
  $toc_year_pages[$path[1]] = True;
  if ( !array_key_exists(3, $path) ) {
    $toc_magazine_pages[$path[1] . $path[2]] = $page;
  } else {
    $toc_sub_pages []= $page;
  }
}

//same as post insert  but no id supplied
$page_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_posts
(post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,
post_name, to_ping, pinged, post_modified, post_modified_gmt,
post_content_filtered, post_type, `post_status`, `post_parent`)
VALUES (?, 
FROM_UNIXTIME(?),
FROM_UNIXTIME(?),
 ?, ?, ?,
?, ?, ?,
FROM_UNIXTIME(?),
FROM_UNIXTIME(?),
?, ?, ?, ?)
');

//insert toc parent page
$wp->beginTransaction();
$page_insert->execute(Array(
  '', #post author
  "1970-1-1 00:00:00", //posted
  "1970-1-1 00:00:00", //posted in gmt
  '', //body
  'Magazine', //title
  '', //post_excerpt
  'mag', //slug
  '', //to ping
  '', //pinged
  "1970-1-1 00:00:00", //changed
  "1970-1-1 00:00:00", //changed in gmt
  '', //post content filtered
  'page', //type
  'publish', //pub status
  0 //parent
));
$toc_parent_id = $wp->lastInsertId();
$wp->commit();
$redirects_needed []= '/mag/';

//insert year parents
$wp->beginTransaction();
foreach ($toc_year_pages as $year => $boolean) { 
  $page_insert->execute(Array(
    '', #post author
    "1970-1-1 00:00:00", //posted
    "1970-1-1 00:00:00", //posted in gmt
    '', //body
    $year, //title
    '', //post_excerpt
    $year, //slug
    '', //to ping
    '', //pinged
    "1970-1-1 00:00:00", //changed
    "1970-1-1 00:00:00", //changed in gmt
    '', //post content filtered
    'page', //type
    'publish', //pub status
    $toc_parent_id //parent
  ));
  $toc_year_pages[$year] = $wp->lastInsertId();
  $redirects_needed []= '/mag/' . $year;
}
$wp->commit();

$months_to_create = Array();
//find months to create
foreach ($toc_sub_pages as $page) { 
  $date = $page['url_split'][1] . $page['url_split'][2];
  $months_to_create[$date] = Array($page['url_split'][1],$page['url_split'][2]);
}
foreach ($toc_magazine_pages as $page) {
  $date = $page['url_split'][1] . $page['url_split'][2];
  $months_to_create[$date] = Array($page['url_split'][1], $page['url_split'][2]);
}
$wp->beginTransaction();
foreach ($months_to_create as $date) { 
  $year = $date[0];
  $month = $date[1];
  $redirects_needed []= '/mag/' . $year . '/' . $month;

  $page_insert->execute(Array(
    '', #post author
    "1970-1-1 00:00:00", //posted
    "1970-1-1 00:00:00", //posted in gmt
    '', //body
    $month,
    '', //post_excerpt
    $month,
    '', //to ping
    '', //pinged
    "1970-1-1 00:00:00", //changed
    "1970-1-1 00:00:00", //changed in gmt
    '', //post content filtered
    'page', //type
    'publish', //pub status
    $toc_year_pages[$year] //parent
  ));
  $toc_month_pages[$year . $month] = $wp->lastInsertId();
}
$wp->commit();

//insert magazine pages
$wp->beginTransaction();
foreach ($toc_magazine_pages as $date => $page) { 
  // form is toc/YYYY/MM/slug
  $page_insert->execute(Array(
    $page['uid'], #post author
    $page['FROM_UNIXTIME(n.created)'], //posted
    $page['FROM_UNIXTIME(n.created)'], //posted
    $page['body'], //body
    $page['title'], //title
    $page['teaser'], //post_excerpt
    'toc', //slug
    '', //to ping
    '', //pinged
    $page['FROM_UNIXTIME(n.changed)'], //posted
    $page['FROM_UNIXTIME(n.changed)'], //posted
    '', //post content filtered
    'page', //type
    $page["IF(n.status = 1, 'publish', 'draft')"], //pub status
    $toc_month_pages[$date] //parent
  ));
}
$wp->commit();


$wp->beginTransaction();
foreach ($toc_sub_pages as $page) { 
  // form is toc/YYYY/MM/slug
  $date = $page['url_split'][1] . $page['url_split'][2];
  $page_insert->execute(Array(
    $page['uid'], #post author
    $page['FROM_UNIXTIME(n.created)'], //posted
    $page['FROM_UNIXTIME(n.created)'], //posted
    $page['body'], //body
    $page['title'], //title
    $page['teaser'], //post_excerpt
    $page['url_split'][3], //slug
    '', //to ping
    '', //pinged
    $page['FROM_UNIXTIME(n.changed)'], //posted
    $page['FROM_UNIXTIME(n.changed)'], //posted
    '', //post content filtered
    'page', //type
    $page["IF(n.status = 1, 'publish', 'draft')"], //pub status
    $toc_month_pages[$page['url_split'][1] . $page['url_split'][2]] //parent
  ));
}
$wp->commit();

$redirect_item_insert = $wp->prepare('
INSERT INTO wp_redirection_items
(url, last_access, group_id, action_type, action_code, action_data, match_type)
VALUES (
?, # source
FROM_UNIXTIME("1970-1-1 00:00:00"), #last access
1,
"url", #action type
301, # action code
"/", #destination action data
"url" #match type
)
;');

$wp->beginTransaction();
foreach ($redirects_needed as $dst) {
  $redirect_item_insert->execute(Array(
    $dst
  ));
}
$wp->commit();


$article_term_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_terms
(name, slug, term_group)
VALUES (
?,
?,
0
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
?,
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


$wp->beginTransaction();
$wp->exec('
INSERT IGNORE INTO pantheon_wp.wp_term_relationships 
(object_id, term_taxonomy_id)
SELECT p.ID, tax.term_taxonomy_id
FROM wp_posts p
JOIN
wp_terms term
ON (p.post_type = term.slug)
JOIN
wp_term_taxonomy tax
ON (tax.term_id = term.term_id)
;'
);
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
$post_content = $d6->prepare("
SELECT DISTINCT 
n.nid,
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
$post_content->execute();

$content_insert = $wp->prepare('
UPDATE wp_posts
SET post_content=?
WHERE ID=?
;
');

$wp->beginTransaction();
while ( $content = $post_content->fetch(PDO::FETCH_NUM)) {
	$content_insert->execute(Array($content[1], $content[0]));
}
$wp->commit();


//for article bodys
$post_content = $d6->prepare('
SELECT DISTINCT 
n.nid,
IF( 
  e.field_article_text_value IS NULL,
  b.field_short_body_value,
  CONCAT(b.field_short_body_value, 
    e.field_article_text_value
  )
)
FROM mjd6.node n
INNER JOIN mjd6.content_field_short_body b
USING(vid)
INNER JOIN mjd6.content_field_article_text e
USING(vid)
WHERE n.type="article"
;
');
$post_content->execute();

$wp->beginTransaction();
while ( $content = $post_content->fetch(PDO::FETCH_NUM)) {
	$content_insert->execute(Array($content[1], $content[0]));
}
$wp->commit();


//for full width bodys
$post_content = $d6->prepare('
SELECT DISTINCT 
n.nid, b.field_short_body_value
FROM mjd6.node n
INNER JOIN mjd6.content_field_short_body b
USING(vid)
WHERE n.type="full_width_article"
;
');
$post_content->execute();

$wp->beginTransaction();
while ( $content = $post_content->fetch(PDO::FETCH_NUM)) {
	$content_insert->execute(Array($content[1], $content[0]));
}
$wp->commit();


$meta_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_postmeta 
(post_id, meta_key, meta_value)
VALUES (?, ?, ?)
;
');

//for dek
$meta_data = $d6->prepare('
SELECT DISTINCT 
n.nid, "mj_dek", d.field_dek_value
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
SELECT DISTINCT n.nid, "mj_dateline_override", d.field_issue_date_value
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
SELECT DISTINCT n.nid, "mj_byline_override", b.field_issue_date_value
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
  $meta_insert->execute( array(
    $meta['nid'],
    'mj_social_hed', 
    $meta['field_social_title_value']
  ) );
  $meta_insert->execute( array(
    $meta['nid'],
    'mj_social_dek', 
    $meta['field_social_dek_value']
  ) );
  $meta_insert->execute( array($meta['nid'], 'mj_google_standout', false) );
  $meta_insert->execute( array($meta['nid'], 'mj_fb_instant_exclude', true) );
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
  $meta_insert->execute( array(
    $meta['nid'],
    'mj_promo_hed',
    $meta['field_alternate_title_value']
  ) );
  $meta_insert->execute( array(
    $meta['nid'],
    'mj_promo_dek',
    $meta['field_alternate_dek_value']
  ) );
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

/*begin author migration */
$wp->beginTransaction();
$wp->exec('
DELETE FROM pantheon_wp.wp_users WHERE ID > 1;
DELETE FROM pantheon_wp.wp_usermeta WHERE user_id > 1;
');
$wp->commit();



//CREATE GUEST AUTHORS FOR ALL USERS

$author_data = $d6->prepare("
SELECT DISTINCT
n.title,
u.uid,
u.mail,
a.field_user_uid, 
a.field_twitter_user_value,
a.field_last_name_value,
a.field_author_bio_short_value,
a.field_author_title_value,
a.field_author_bio_value,
a.field_photo_fid,
a.field_author_title_value
FROM mjd6.content_field_byline b
INNER JOIN mjd6.node n
ON (n.nid = b.field_byline_nid)
INNER JOIN mjd6.content_type_author a 
ON (a.vid = n.vid)
LEFT JOIN mjd6.users u
ON (u.uid=a.field_user_uid)
WHERE n.title IS NOT NULL
;
");
$author_data->execute();


$author_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_users
(user_nicename, user_login, user_registered, display_name)
VALUES (
  REPLACE(LOWER(?), " ", "-"), # NICENAME lowercase, - instead of space
  REPLACE(LOWER(?), " ", ""), # login lowercase, no spaces
  "0000-00-00 00:00:00", # registered date
  ? # Display name
)
');


$uid_to_author_meta = Array();
$author_name_to_author_meta = Array();
$wp->beginTransaction();
while ( $author = $author_data->fetch(PDO::FETCH_ASSOC)) {
  $author_insert->execute(Array(
    $author['title'],
    $author['title'],
    $author['title']
  ));
  $uid_to_author_meta[$wp->lastInsertId()] = $author;
  $author['wp_id'] = $wp->lastInsertId();
  $author_name_to_author_meta[$author['title']] = $author;
}
$wp->commit();

$roles_data = $d6->prepare("
SELECT DISTINCT
u.name
FROM mjd6.users u
INNER JOIN mjd6.users_roles r
USING (uid)
;
");
$roles_data->execute();

$wp->beginTransaction();
while ( $author = $roles_data->fetch(PDO::FETCH_ASSOC)) {
  if ( array_key_exists( $author['name'], $author_name_to_author_meta ) ) {
    continue;
  }
  $author_insert->execute(Array(
    $author['name'],
    $author['name'],
    $author['name']
  ));
  $author['wp_id'] = $wp->lastInsertId();
  $author_name_to_author_meta[$author['name']] = $author;
  $uid_to_author_meta[$wp->lastInsertId()] = $author;

}
$wp->commit();

$author_meta_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_usermeta (user_id, meta_key, meta_value)
VALUES ( ?, ?, ? )
;
");

$author_meta_insert->bindParam(1, $uiid);
$author_meta_insert->bindParam(2, $key);
$author_meta_insert->bindParam(3, $value);
$wp->beginTransaction();
foreach ( $uid_to_author_meta as $uid => $author ) {
  $uiid = $uid;

  if (array_key_exists('field_twitter_user_value', $author)) {
    $key = "mj_user_twitter";
    $value = $author['field_twitter_user_value'];
    $author_meta_insert->execute();
  }

  if (array_key_exists('mj_user_short_bio', $author)) {
    $key = "mj_user_short_bio";
    $value = $author['field_author_bio_short_value'];
    $author_meta_insert->execute();
  }

  if (array_key_exists('field_author_bio_value', $author)) {
    $key = "mj_user_bio";
    $value = $author['field_author_bio_value'];
    $author_meta_insert->execute();
  }

  if (array_key_exists('field_author_title_value', $author)) {
    $key = "mj_user_position";
    $value = $author['field_author_title_value'];
    $author_meta_insert->execute();
  }

}
$wp->commit();


//Create byline tags 

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

$term_id_to_name = array();
$wp->beginTransaction();
while ( $byline = $byline_titles_data->fetch(PDO::FETCH_ASSOC)) {
  $byline_titles_insert->execute(Array(
    $byline['title'],
    $byline['title']
  ));
  $term_id_to_name[$wp->lastInsertId()] = $byline['title']; 
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

$name_to_tax_id = array();
$wp->beginTransaction();
foreach ( $term_id_to_name as $term_id => $name ) {
  $author_meta = $author_name_to_author_meta[$name];
  $description = $name 
    . ' ' . $author_meta['field_last_name_value']
    . ' ' . $name
    . ' ' . $author_meta['wp_id']
    . ' ' . $author_meta['mail']
  ;
  $byline_taxonomy_insert->execute(Array(
    $term_id,
    $description
  ));
  $name_to_tax_id[$name] = $wp->lastInsertId();
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
INNER JOIN mjd6.node a
ON (b.vid = a.vid)
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
  if (array_key_exists($term['title'], $author_name_to_author_meta)) {
    $byline_term_insert->execute(Array(
      $term['node'],
      $name_to_tax_id[$term['title']]
    ));
  }
}
$wp->commit();

// end create byline taxonomy terms



//everybody is a contributor! Later we can make active users active
//
$wp->beginTransaction();
$user_insert = $wp->exec("
INSERT IGNORE INTO pantheon_wp.wp_usermeta (user_id, meta_key, meta_value)
SELECT DISTINCT
ID, 'wp_capabilities', 'a:1:{s:13:\"former_author\";s:1:\"1\";}'
FROM pantheon_wp.wp_users
;
");
$wp->commit();

//author roles who are active users
$roles_data = $d6->prepare("
SELECT DISTINCT
u.name
FROM mjd6.users u
INNER JOIN mjd6.users_roles r
USING (uid)
WHERE u.mail IS NOT NULL
;
");
$roles_data->execute();

$roles_insert = $wp->prepare("
UPDATE pantheon_wp.wp_usermeta 
SET meta_value = 'a:1:{s:6:\"editor\";s:1:\"1\";}'
WHERE meta_key = 'wp_capabilities'
AND user_id = ?
;
");
$wp->beginTransaction();
while ( $role = $roles_data->fetch(PDO::FETCH_ASSOC)) {
  $user_id = $author_name_to_author_meta[$role['name']]['wp_id'];
	$roles_insert->execute(Array($user_id));
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

//author photo
$author_image_data = $d6->prepare("
SELECT DISTINCT
n.nid,
a.field_user_uid, 
f.filemime,
f.filepath,
f.filename,
n.title,
a.field_photo_fid
FROM mjd6.node n
INNER JOIN mjd6.content_type_author a 
USING(vid)
INNER JOIN mjd6.files f
ON(a.field_photo_fid = f.fid)
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
FROM_UNIXTIME("1970-1-1 00:00:00"),
FROM_UNIXTIME("1970-1-1 00:00:00"),
"",
:post_title,
"",
:post_name,
"",
"",
FROM_UNIXTIME("1970-1-1 00:00:00"),
FROM_UNIXTIME("1970-1-1 00:00:00"),
:guid,
"",
"attachment",
"publish",
NULL,
:post_mime_type
)
;
');


$wp->beginTransaction();
while ( $image = $author_image_data->fetch(PDO::FETCH_ASSOC)) {
  $uid  = $author_name_to_author_meta[$image['title']]['wp_id'];
  $guid = $FILEDIR_ABS . preg_replace('/files\//', '', $image['filepath']);
  $author_image_insert->execute(array(
    ':post_author' => $uid,
    ':post_title' => $image['filename'],
    ':post_name' => $image['filename'],
    ':guid' => $guid,
    ':post_mime_type' => $image['filemime'],
  ));
  $author_name_to_author_meta[$image['title']]['image_location'] = 
     preg_replace('/files\//', $FILEDIR, $image['filepath']);
  $author_name_to_author_meta[$image['title']]['image_id'] = $wp->lastInsertId();
}
$wp->commit();

$author_meta_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_usermeta (user_id, meta_key, meta_value)
VALUES ( ?, ?, ? )
;
");

$wp->beginTransaction();
foreach ( $author_name_to_author_meta as $author ) {
  if ( array_key_exists('image_id', $author) ) {
    $author_meta_insert->execute(array(
      $author['wp_id'],
      "author_image_id",
      $author['image_id']
    ));

    $author_meta_insert->execute(array(
      $author['image_id'],
      '_wp_attached_file',
      $author['image_location']
    ) );
  }
}
$wp->commit();
echo "authors done";


/* end author data */


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
f.filepath,
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
FROM_UNIXTIME(:post_date), #post date
FROM_UNIXTIME(:post_date),
"", #post content (description)
:post_title,
:post_excerpt, 
:post_name,
"",
"",
FROM_UNIXTIME(:post_modified),
FROM_UNIXTIME(:post_modified),
:guid,
"",
"attachment",
IF(:status = 1, "publish", "draft"),
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

  $guid = preg_replace('/files\//', $FILEDIR_ABS, $master['filepath']);
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
    ':post_excerpt' => $master['field_master_image_caption_value'],

  ) );


  $master_meta_rows[] = array(
    'nid' => $master['nid'],
    'image_id' => $wp->lastInsertId(),
    'filepath' => preg_replace('/files\//', $FILEDIR, $master['filepath']),
    'master_image' => $wp->lastInsertId(),
    'master_image_byline' => $master['field_art_byline_value'],
    'master_image_suppress' => $master['field_suppress_master_image_value'],
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
    'featured-image-display',
    $row['master_image_suppress']
  ) );

  $master_meta_insert->execute(array(
    $row['nid'],
    '_thumbnail_id',
    $row['image_id']
  ) );

  $master_meta_insert->execute(array(
    $row['image_id'],
    '_wp_attached_file',
    $row['filepath']
  ) );

  $master_meta_insert->execute(array(
    $row['image_id'],
    '_media_credit',
    $row['master_image_byline']
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
f.filepath,
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
IF(:status = 1, "publish", "draft"),
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

  $guid = preg_replace('/files\//', $FILEDIR_ABS, $title['filepath']);
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



  $title_meta_rows[] = array(
    'nid' => $title['nid'],
    'image_id' => $wp->lastInsertId(),
    'filename' => $title['filename'],
    'filepath' => preg_replace('/files\//', $FILEDIR, $title['filepath']),
    'title_image_credit' => $title['field_title_image_credit_value'],
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

  $title_image_array = Array( mj_title_image => $row['image_id'] );
  $title_meta_insert->execute(array(
    $row['nid'],
    'mfi-reloaded-images',
    serialize($title_image_array)
  ) );

  $title_meta_insert->execute(array(
    $row['image_id'],
    '_wp_attached_file',
    $row['filepath']
  ) );

  $master_meta_insert->execute(array(
    $row['image_id'],
    '_media_credit',
    $row['title_image_credit']
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
f.filepath,
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
IF(:status = 1, "publish", "draft"),
:post_parent,
:post_mime_type
)
;
');

$file_meta_rows = array();
$node_file_rows = array();

$wp->beginTransaction();
while ( $file = $file_data->fetch(PDO::FETCH_ASSOC)) {

  $guid = preg_replace('/files\//', $FILEDIR_ABS, $file['filepath']);
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
    'filepath' => preg_replace('/files\//', $FILEDIR, $title['filepath']),
    'filename' => $file['filename']
  );

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

  $file_meta_insert->execute(array(
    $row['nid'],
    'file_attachment',
    $row['fid']
  ) );
}
$wp->commit();

echo "files done";

// do zoninator

$zones = Array(
  'top_stories' => Array(
    324446, 324431, 324441, 324436, 324531,
    324576, 324296, 324626, 324616, 324426, 324586
  ),
  'homepage_featured' => Array(324801)
);
$zone_descriptions = Array(
  'top_stories' => Array('description' => "For placement on the homepage, top story widget"),
  'homepage_featured' => Array('description' => "Controls 'Featured' section on the homepage")
);

foreach ($zones as $zone => $queue) {
  $zone_term_insert = $wp->prepare('
  INSERT IGNORE INTO wp_terms
  (name, slug)
  VALUES (?, ?)
  ;
  ');
  $wp->beginTransaction();
  $zone_term_insert->execute(array($zone, $zone));

  $zone_term_id = $wp->lastInsertId();
  $wp->commit();

  $zone_tax_insert = $wp->prepare('
  INSERT IGNORE INTO wp_term_taxonomy
  (term_id, taxonomy, description)
  VALUES (?, "zoninator_zones", ?)
  ;
  ');


  $description = $zone_descriptions[$zone];

  $wp->beginTransaction();
  $zone_tax_insert->execute(array(
    $zone_term_id, 
    serialize($description)
  ));
  $zone_tax_id = $wp->lastInsertId();
  $wp->commit();



  $zone_meta_insert = $wp->prepare('
  INSERT IGNORE INTO wp_postmeta
  (post_id, meta_key, meta_value)
  VALUES (?, ?, ?)
  ;
  ');

  $meta_key = '_zoninator_order_' . $zone_term_id;

  $wp->beginTransaction();
  for ($i = 0; $i < count($queue); $i++) {
    $zone_meta_insert->execute(Array(
      $queue[$i],
      $meta_key,
      ($i + 1)
    ));
  }
  $wp->commit();

}
echo "zoninator filled";

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
?>
