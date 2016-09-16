<?php
$hostname="localhost";  
$username="root";   
$password="p";  

$d6_db = "mjd6";  
$d6 = new PDO("mysql:host=$hostname;dbname=$d6_db", $username, $password);  

$wp_db = "pantheon_wp";  
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

?>
