<?php
/**
 * @package Mother Jones Content Fields
 * @version 0.1
 *
 * wherein we define the fields that our custom types need
 */

$byline_override = array(
  'label' => __('Byline Override'),
  'input' => 'text',
  'value' => get_post_meta($post->ID, "byline_override", true),
  'helps' => "Use this if you want something other than a list of our authors",
);

function add_article_fields_to_edit() {
  $form_fields["byline_override"] = $byline_override;
}
add_filter("mj_article_fields_to_edit", "add_article_fields_to_edit", null, 2);

//start smaller
add_filter("post_fields_to_edit", "add_article_fields_to_edit", null, 2);


?>
