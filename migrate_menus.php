<?php

$hostname="localhost";  
$username="root";   
$password=$argv[1];
$wp_db = "pantheon_wp";  

$wp = new PDO("mysql:host=$hostname;dbname=$wp_db", $username, $password);  

$static = Array(
  'name' => 'Static Navbar',
  'slug' => 'static-navbar', 
  'term_id' => '',
  'term_taxonomy_id' => '',
  'menu_items' => Array(
    Array(
      'post_title' => 'Politics',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 1,
        '_menu_item_object' => 'category',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Environment',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 2,
        '_menu_item_object' => 'category',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Food',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 16734,
        '_menu_item_object' => 'category',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Media',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 3,
        '_menu_item_object' => 'category',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Crime &amp; Justice',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 16720,
        '_menu_item_object' => 'category',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Photos',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 22221,
        '_menu_item_object' => 'post_tag',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => '',
      )
    ),
    Array(
      'post_title' => 'Investigations',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 23611,
        '_menu_item_object' => 'post_tag',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => '',
      )
    ),
    Array(
      'post_title' => 'Magazine',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'custom',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => '',
        '_menu_item_object' => 'custom',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => '/magazine'
      )
    ),
  )
);
$floating = Array(
  'name' => 'Floating Navbar',
  'slug' => 'floating-nav', 
  'term_id' => '',
  'term_taxonomy_id' => '',
  'menu_items' => Array(
    Array(
      'post_title' => 'Search',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'custom',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => '',
        '_menu_item_object' => 'custom',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => '/search'
      )
    ),
    Array(
      'post_title' => 'Newsletter',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'post_type',
        '_menu_item_menu_item_parent' => 64,
        '_menu_item_object_id' => 319626,
        '_menu_item_object' => 'page',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Politics',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 1,
        '_menu_item_object' => 'category',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Environment',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 2,
        '_menu_item_object' => 'category',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Media',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 3,
        '_menu_item_object' => 'category',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Crime and Justice',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 16720,
        '_menu_item_object' => 'category',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Food',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 16734,
        '_menu_item_object' => 'category',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Guns',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 22171,
        '_menu_item_object' => 'post_tag',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => '',
      )
    ),
    Array(
      'post_title' => 'Dark Money',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 20426,
        '_menu_item_object' => 'post_tag',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => '',
      )
    ),
    Array(
      'post_title' => 'Photos',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 22221,
        '_menu_item_object' => 'post_tag',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => '',
      )
    ),
    Array(
      'post_title' => 'Investigations',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 23611,
        '_menu_item_object' => 'post_tag',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => '',
      )
    ),
    Array(
      'post_title' => 'Podcasts',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'custom',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => '',
        '_menu_item_object' => 'custom',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => '/podcasts'
      )
    ),
    Array(
      'post_title' => 'Kevin Drum',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'taxonomy',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 14,
        '_menu_item_object' => 'category',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => '',
      )
    ),
    Array(
      'post_title' => 'About',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'post_type',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 64,
        '_menu_item_object' => 'page',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Subscribe',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'custom',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => '',
        '_menu_item_object' => 'custom',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => 'https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN1A&base_country=US'
      )
    ),
    Array(
      'post_title' => 'Donate',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'custom',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => '',
        '_menu_item_object' => 'custom',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => 'https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGP003&extra_don=1&abver=A'
      )
    ),
  )
);
$footer = Array(
  'name' => 'Footer List',
  'slug' => 'footer-list', 
  'term_id' => '',
  'term_taxonomy_id' => '',
  'menu_items' => Array(
    Array(
      'post_title' => 'About Us',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'post_type',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => 64,
        '_menu_item_object' => 'page',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Store',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'custom',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => '',
        '_menu_item_object' => 'custom',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => 'http://store.motherjones.com/?utm_source=motherjones&utm_medium=footer&utm_content=orangefooterlink&utm_campaign=evergreen'
      )
    ),
    Array(
      'post_title' => 'Donate',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'custom',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => '',
        '_menu_item_object' => 'custom',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => 'https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGP003&extra_don=1&abver=A'
      )
    ),
    Array(
      'post_title' => 'Subscribe',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'custom',
        '_menu_item_menu_item_parent' => 0,
        '_menu_item_object_id' => '',
        '_menu_item_object' => 'custom',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => 'https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN1A&base_country=US'
      )
    ),
    Array(
      'post_title' => 'Customer Service',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'post_type',
        '_menu_item_menu_item_parent' => 64,
        '_menu_item_object_id' => 76,
        '_menu_item_object' => 'page',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Advertise',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'post_type',
        '_menu_item_menu_item_parent' => 64,
        '_menu_item_object_id' => 19260,
        '_menu_item_object' => 'page',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
  )
);
$copyright = Array(
  'name' => 'Copyright',
  'slug' => 'copyright', 
  'term_id' => '',
  'term_taxonomy_id' => '',
  'menu_items' => Array(
    Array(
      'post_title' => 'Terms of Service',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'post_type',
        '_menu_item_menu_item_parent' => 64,
        '_menu_item_object_id' => 19263,
        '_menu_item_object' => 'page',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Privacy Policy',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'post_type',
        '_menu_item_menu_item_parent' => 64,
        '_menu_item_object_id' => 626,
        '_menu_item_object' => 'page',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
    Array(
      'post_title' => 'Contact Us',
      'post_id' => '',
      'meta' => Array(
        '_menu_item_type' => 'post_type',
        '_menu_item_menu_item_parent' => 64,
        '_menu_item_object_id' => 565,
        '_menu_item_object' => 'page',
        '_menu_item_target' => '',
        '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
        '_menu_item_xfn' => '',
        '_menu_item_url' => ''
      )
    ),
  )
);

$menus = Array($copyright, $static, $floating, $footer);

$term_insert = $wp->prepare('
INSERT INTO wp_terms
(`name`, slug, term_group)
VALUES (
?, REPLACE(LOWER(?), " ", "-"), 0
)
;'
);
$wp->beginTransaction();
for($i = 0; $i < sizeof($menus); $i++) {
  $menu = $menus[$i];
  $term_insert->execute(Array($menu['name'], $menu['slug']));
  $menus[$i]['term_id'] = $wp->lastInsertId();
}
$wp->commit();

$tax_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_term_taxonomy
(term_id, taxonomy, description, parent)
VALUES (
?,
"nav_menu",
"",
0
)
;'
);
$wp->beginTransaction();
for($i = 0; $i < sizeof($menus); $i++) {
  $menu = $menus[$i];
  $tax_insert->execute(Array($menu['term_id']));
  $menus[$i]['tax_id'] = $wp->lastInsertId();
}
$wp->commit();

$post_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_posts
(post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,
post_name, to_ping, pinged, post_modified, post_modified_gmt,
post_content_filtered, post_type, `post_status`, post_parent, post_mime_type,
menu_order)
VALUES (
1,
FROM_UNIXTIME("2017-04-03 16:42:58"),
CONVERT_TZ(FROM_UNIXTIME("2017-04-03 16:42:58"), "PST8PDT","UTC"),
"",
:post_title,
"",
REPLACE(LOWER(:post_name), " ", "-"),
"",
"",
FROM_UNIXTIME("2017-04-03 16:42:58"),
CONVERT_TZ(FROM_UNIXTIME("2017-04-03 16:42:58"), "PST8PDT","UTC"),
"",
"nav_menu_item",
"publish",
:parent,
"",
:menu_order
)
;
');

$wp->beginTransaction();
for($i = 0; $i < sizeof($menus); $i++) {
  $menu = $menus[$i];
  for($l = 0; $l < sizeof($menu['menu_items']); $l++) {
    $item = $menu['menu_items'][$l];

    $post_insert->execute(Array(
      ':post_title' => $item['post_title'],
      ':post_name' => $item['post_title'],
      ':parent' => $item['meta']['_menu_item_menu_item_parent'],
      ':menu_order' => $l,
    ));
    $menus[$i]['menu_items'][$l]['post_id'] = $wp->lastInsertId();
  }
}
$wp->commit();

$menu_meta_insert = $wp->prepare("
INSERT IGNORE INTO pantheon_wp.wp_postmeta
(post_id, meta_key, meta_value)
VALUES (?, ?, ?)
;
");
$wp->beginTransaction();
for($i = 0; $i < sizeof($menus); $i++) {
  $menu = $menus[$i];
  for($l = 0; $l < sizeof($menu['menu_items']); $l++) {
    $item = $menu['menu_items'][$l];

    foreach ($item['meta'] as $key => $value) {
      if ($value) {
        $menu_meta_insert->execute(Array(
          $item['post_id'],
          $key,
          $value
        ));
      } //has value
    } //meta loop
  } //item loop
} //menu loop
$wp->commit();

$term_rel_insert = $wp->prepare('
INSERT IGNORE INTO pantheon_wp.wp_term_relationships
(object_id, term_taxonomy_id)
VALUES (?, ?)
');
$wp->beginTransaction();
for($i = 0; $i < sizeof($menus); $i++) {
  $menu = $menus[$i];
  for($l = 0; $l < sizeof($menu['menu_items']); $l++) {
    $item = $menu['menu_items'][$l];

    $term_rel_insert->execute(Array(
      $item['post_id'],
      $menu['tax_id']
    ));
  }
}
$wp->commit();
/*
WP TERMS
menu categories go into wp_terms with id, name is Display Name, slug is same but 
  - instead of spaces and lowercase

WP TAXONOMYS
each of those terms need an entry in wp_term_taxonomy with the term id from
above, and taxonomy 'nav_menu'

WP POSTS
for menu items. each needs
needs a post_title of the display name of the menu item, post name same but
  - instead of spaces and lowercase
  menu_order (yep)
  post_type of nav_menu_item

WP POSTMETA
for menu items, each needs
  post_id of the post id above, and entries for
        '_menu_item_type' => meta_value (custom|taxonomy|post_type)
        '_menu_item_menu_item_parent' => 0 (?maybe different when I have >1 menus
        '_menu_item_object_id' => the post id for custom, the term id for categories the page id for pages
        '_menu_item_object' => (category|custom|page)
        '_menu_item_target' => ''
        '_menu_item_classes' => a:1:{i:0;s:0:"";}
        '_menu_item_xfn' => ''
        '_menu_item_url' => url for custom, '' for category

WP TERM RELATIONSHIPS
has an object_id w/ the id of the menu item
a term_taxonomy_id w/ the taxonomy id from above, and
a term_order, always 0
 */
