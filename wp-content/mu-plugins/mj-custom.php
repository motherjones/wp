<?php
//FIXME!! needs scheduling (issue date?) taxonomy, file attachments
/*
  Plugin Name: Mother Jones Custom
  Description: Call in everything that makes the site MJ
  Version: 0.1
  Author: Mother Jones
  Author URI: http://www.motherjones.com
*/


//Add custom types for mj_article, mj_blog_post, mj_full_width
require_once('mj_custom/motherjones-content-types.php');
MJ_Custom_Types()->setup();


?>
