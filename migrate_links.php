<?php
$hostname="localhost";  
$username="root";   
$password=$argv[1];
$d6_db = "mjd6";  
$wp_db = "pantheon_wp";  


$d6 = new PDO("mysql:host=$hostname;dbname=$d6_db", $username, $password);  

$wp = new PDO("mysql:host=$hostname;dbname=$wp_db", $username, $password);  

$wp->beginTransaction();
$wp->exec(
			"CREATE TABLE IF NOT EXISTS `wp_redirection_items`(
				`id` int(11) unsigned NOT NULL auto_increment,
			  `url` mediumtext NOT NULL,
			  `regex` int(11) unsigned NOT NULL default '0',
			  `position` int(11) unsigned NOT NULL default '0',
			  `last_count` int(10) unsigned NOT NULL default '0',
			  `last_access` datetime NOT NULL,
			  `group_id` int(11) NOT NULL default '0',
			  `status` enum('enabled','disabled' ) NOT NULL default 'enabled',
			  `action_type` varchar(20) NOT NULL,
			  `action_code` int(11) unsigned NOT NULL,
			  `action_data` mediumtext,
			  `match_type` varchar(20) NOT NULL,
			  `title` varchar(50) NULL,
			  PRIMARY KEY ( `id`),
				KEY `url` (`url`(200)),
			  KEY `status` (`status`),
			  KEY `regex` (`regex`),
				KEY `group_idpos` (`group_id`,`position`),
			  KEY `group` (`group_id`)
      );"
);
$wp->commit();

$wp->beginTransaction();
$wp->exec(
			"CREATE TABLE IF NOT EXISTS `wp_redirection_groups`(
			  `id` int(11) NOT NULL auto_increment,
			  `name` varchar(50) NOT NULL,
			  `tracking` int(11) NOT NULL default '1',
			  `module_id` int(11) unsigned NOT NULL default '0',
		  	`status` enum('enabled','disabled' ) NOT NULL default 'enabled',
		  	`position` int(11) unsigned NOT NULL default '0',
			  PRIMARY KEY ( `id`),
				KEY `module_id` (`module_id`),
		  	KEY `status` (`status`)
			);"
);
$wp->commit();

$wp->beginTransaction();
$wp->exec(
			"CREATE TABLE IF NOT EXISTS `wp_redirection_logs`(
			  `id` int(11) unsigned NOT NULL auto_increment,
			  `created` datetime NOT NULL,
			  `url` mediumtext NOT NULL,
			  `sent_to` mediumtext,
			  `agent` mediumtext NOT NULL,
			  `referrer` mediumtext,
			  `redirection_id` int(11) unsigned default NULL,
			  `ip` varchar(17) NOT NULL default '',
			  `module_id` int(11) unsigned NOT NULL,
				`group_id` int(11) unsigned default NULL,
			  PRIMARY KEY ( `id`),
			  KEY `created` (`created`),
			  KEY `redirection_id` (`redirection_id`),
			  KEY `ip` (`ip`),
			  KEY `group_id` (`group_id`),
			  KEY `module_id` (`module_id`)
      ); "
);
$wp->commit();

$wp->beginTransaction();
$wp->exec(
			"CREATE TABLE IF NOT EXISTS `wp_redirection_404` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `created` datetime NOT NULL,
			  `url` varchar(255) NOT NULL DEFAULT '',
			  `agent` varchar(255) DEFAULT NULL,
			  `referrer` varchar(255) DEFAULT NULL,
			  `ip` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `created` (`created`),
			  KEY `url` (`url`),
			  KEY `ip` (`ip`),
			  KEY `referrer` (`referrer`)
		  	);"
		);
$wp->commit();

//truncate the tables
$wp->beginTransaction();
$wp->exec('
TRUNCATE pantheon_wp.wp_redirection_404;
TRUNCATE pantheon_wp.wp_redirection_logs;
TRUNCATE pantheon_wp.wp_redirection_groups;
TRUNCATE pantheon_wp.wp_redirection_items;
');
$wp->commit();

//INSERT REDIRECTION GROUPS
$wp->beginTransaction();
$wp->exec('
INSERT INTO wp_redirection_groups
(name, module_id, position)
"Redirections",
1,
0
;
');
$wp->commit();
$wp->beginTransaction();
$wp->exec('
INSERT INTO wp_redirection_groups
(name, module_id, position)
"Modified Posts",
1,
1
;
');
$wp->commit();

$wp->beginTransaction();
$wp->exec('
REPLACE INTO wp_options
(option_name, option_value, autoload)
VALUES (
"redirection_options",
\'a:2:{s:12:"monitor_post";i:2;s:16:"monitor_category";i:2;}\'
)
;'
);
$wp->commit();


$redirect_item_insert = $wp->prepare('
INSERT INTO wp_redirection_items
(url, last_access, group_id, action_type, action_code, action_data, match_type)
VALUES (
CONCAT("/", ?), # source
FROM_UNIXTIME("1970-1-1 00:00:00"), #last access
1,
"url", #action type
301, # action code
?, #destination action data
"url" #match type
)
;');

//GET LEGACY REDIRECTS
$legacy_redirects = $d6->prepare('
SELECT
src,
dst
FROM mjd6.legacy_redirect
;'
);
$legacy_redirects->execute();

$wp->beginTransaction();
while ( $redirect = $legacy_redirects->fetch(PDO::FETCH_NUM)) {
	$redirect_item_insert->execute($redirect);
}
$wp->commit();

// redirect photoessay page
$wp->beginTransaction();
$redirect_item_insert->execute(Array('photoessays', 'topics/photoessays'));
$wp->commit();


//GET MANUAL REDIRECTS
$manual_redirects = $d6->prepare('
SELECT source, redirect FROM path_redirect 
WHERE redirect NOT LIKE "node%"
AND source != redirect
;'
);
$manual_redirects->execute();

$wp->beginTransaction();
while ( $redirect = $manual_redirects->fetch(PDO::FETCH_NUM)) {
	$redirect_item_insert->execute($redirect);
}
$wp->commit();

//UPDATE PAGES WITH SLASHES IN THEM

$page_redirects = $d6->prepare('
SELECT DISTINCT
a.dst,
REPLACE(
	a.dst,
    "/",
    "-"
)
FROM mjd6.node n
INNER JOIN mjd6.node_revisions r
USING(vid)
LEFT OUTER JOIN mjd6.url_alias a
ON a.src = CONCAT("node/", n.nid)
WHERE n.type = "page"
AND a.dst NOT LIKE "%about%"
AND a.dst NOT LIKE "%toc%"
AND a.dst LIKE "%/%"
AND n.status = 1
;
');
$page_redirects->execute();

$wp->beginTransaction();
while ( $redirect = $page_redirects->fetch(PDO::FETCH_NUM)) {
	$redirect_item_insert->execute($redirect);
}
$wp->commit();

$page_redirects = $d6->prepare('
SELECT DISTINCT
a.dst,
CONCAT("/about/",
	REPLACE(
	  SUBSTR(a.dst, 
		LOCATE("/", a.dst) + 1
	  ), 
	  "/",
	  "-"
	)
)
FROM mjd6.node n
INNER JOIN mjd6.node_revisions r
USING(vid)
LEFT OUTER JOIN mjd6.url_alias a
ON a.src = CONCAT("node/", n.nid)
WHERE n.type = "page"
AND a.dst LIKE "about/%/%"
AND n.status = 1
;
');
$page_redirects->execute();

$wp->beginTransaction();
while ( $redirect = $page_redirects->fetch(PDO::FETCH_NUM)) {
	$redirect_item_insert->execute($redirect);
}
$wp->commit();

/**
 * GET POSTS WITH THE WRONG MONTH IN THE URL
 * drupal makes the url w/ the month set by created date, not post date
 * So we're gonna make those old urls point to new good urls
 */

$month_redirects = $d6->prepare('
SELECT DISTINCT n.nid,
r.dst
FROM mjd6.url_alias r
JOIN mjd6.node n
ON ( n.nid = REPLACE(r.src, "node/", "") )
JOIN mjd6.publication_date p
ON ( n.nid = p.nid )
WHERE r.src LIKE "node%" AND r.src NOT LIKE "%feed"
AND n.status = 1 AND 
(n.type = "article" OR n.type = "blogpost" OR n.type = "full_width_article")
AND 
MONTH( FROM_UNIXTIME(p.published_at) )
!=
TRIM( LEADING "0" FROM
  SUBSTRING_INDEX(
	  SUBSTRING_INDEX( r.dst, "/", -2 ),
  "/", 1)
)
;'
);
$month_redirects->execute();

$wp->beginTransaction();
while ($redirect = $month_redirects->fetch(PDO::FETCH_ASSOC)) {
	$redirect_item_insert->execute(Array(
		$redirect['dst'],
		'/?p=' . $redirect['nid'],
	));
}
$wp->commit();
