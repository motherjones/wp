<?php
$FILEDIR = "http://dev-mjben.pantheonsite.io/wp-content/uploads/";

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

//FIXME ADD _wp_attached_file to posts_metadata w/ a key of something like
//  2016/06/24m6dk0.jpg
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
?>
