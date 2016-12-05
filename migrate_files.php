<?php
$hostname="localhost";  
$username="root";   
$password="p";  
$d6_db = "mjd6";  
$wp_db = "pantheon_wp";  
$FILEDIR = "http://dev-mjben.pantheonsite.io/wp-content/uploads/";

$d6 = new PDO("mysql:host=$hostname;dbname=$d6_db", $username, $password);  

$wp = new PDO("mysql:host=$hostname;dbname=$wp_db", $username, $password);  

$user_data = $d6->prepare("
SELECT DISTINCT
u.uid, 'wp_capabilities', 'a:1:{s:13:\"former_author\";s:1:\"1\";}'
FROM mjd6.users u
;"
);
$user_data->execute();

$user_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_usermeta (user_id, meta_key, meta_value)
VALUES ( ?, ?, ? )
;
");

$wp->beginTransaction();
while ( $user = $user_data->fetch(PDO::FETCH_NUM)) {
	$user_insert->execute($user);
}
$wp->commit();


?>
