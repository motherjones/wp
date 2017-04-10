<?php
/*
Plugin Name: Taxonomy Converter
Description: Copy or convert terms between taxonomies.
Author: kristarella
Author URI: http://www.kristarella.com
Version: 1.1
License: GPLv2 or later
*/

if ( !defined('WP_LOAD_IMPORTERS') )
	return;

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

if ( !class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) )
		require_once $class_wp_importer;
}

/**
 * Taxonomy Converter
 *
 * @package plugin
 * @subpackage Importer
 */
if ( class_exists( 'WP_Importer' ) ) {
class WP_Taxonomy_Converter extends WP_Importer {
	var $terms_to_convert = array();
	var $taxes = array();
	var $all_terms = array();
	var $hybrids_ids = array();

	function header($current='category') {
		
		$this->populate_taxes();
	
		echo '<div class="wrap">';
		if ( ! current_user_can('manage_categories') ) {
			echo '<div class="narrow">';
			echo '<p>' . __('Cheatin&#8217; uh?', 'wptaxconvert') . '</p>';
			echo '</div>';
		} else { ?>
			<h2 class="nav-tab-wrapper">
			<?php foreach ($this->taxes as $name => $tax) {
				if ($name == $current)
					$classes = 'nav-tab nav-tab-active';
				else
					$classes = 'nav-tab';
			?>
			<a class="<?php echo $classes; ?>" href="admin.php?import=wptaxconvert&amp;tax=<?php echo $name; ?>"><?php echo $tax->label; ?></a> 
			<?php } ?>
			</h2>
		<?php }
	}

	function footer() {
		echo '</div>';
	}
	
	function populate_taxes() {
		$taxonomies = get_taxonomies(array('public' => true),'names'); 
		foreach ( $taxonomies as $taxonomy ) {
			if ($taxonomy !== 'post_format')
				$this->taxes[$taxonomy] = get_taxonomy($taxonomy);
		}
	}

	function populate_tax($tax) {
		$terms = get_terms( array($tax), array('get' => 'all') );
		foreach ( $terms as $term ) {
			$this->all_terms[$tax][] = $term;
			if ( is_array(term_exists( $term->slug )) )
				$this->hybrids_ids[] = $term->term_id;
		}
	}

	function tabs($tax) {
		$this->populate_tax($tax);
		$num = count($this->all_terms[$tax]);
		$details = $this->taxes[$tax];

		echo '<br class="clear" />';

		if ( $num > 0 ) {
			screen_icon();
			echo '<h2>' . __( 'Convert or Copy '.$details->label, 'wptaxconvert') . '</h2>';
			echo '<div class="narrow">';
			echo '<p>' . __('Here you can selectively copy or convert existing terms from one taxonomy to another. To get started, choose the original taxonomy (above), choose option to copy or convert, select the terms (below), then click the Go button.', 'wptaxconvert') . '</p>';
			echo '<p>' . __('Keep in mind that "converted" terms are deleted from original taxonomy, and if you convert a term with children, the children become top-level orphans.', 'wptaxconvert') . '</p></div>';

			$this->terms_form($tax);
		} else {
			echo '<p>'.__('You have no terms to convert', 'wptaxconvert').'</p>';
		}
	}

	function terms_form($tax) { ?>

<script type="text/javascript">
/* <![CDATA[ */
var checkflag = "false";
function check_all_rows() {
	field = document.term_list;
	if ( 'false' == checkflag ) {
		for ( i = 0; i < field.length; i++ ) {
			if ( 'terms_to_convert[]' == field[i].name )
				field[i].checked = true;
		}
		checkflag = 'true';
		return '<?php _e('Uncheck All', 'wptaxconvert') ?>';
	} else {
		for ( i = 0; i < field.length; i++ ) {
			if ( 'terms_to_convert[]' == field[i].name )
				field[i].checked = false;
		}
		checkflag = 'false';
		return '<?php _e('Check All', 'wptaxconvert') ?>';
	}
}
/* ]]> */
</script>

<form name="term_list" id="term_list" action="admin.php?import=wptaxconvert&amp;tax=<?php echo $tax; ?>&amp;step=2" method="post">

	<p><label><input type="radio" name="convert" value="0" checked="checked" /> Copy</label> &emsp; <label><input type="radio" name="convert" value="1" /> Convert</label></p>
<p>To taxonomy:
<?php
	foreach ($this->taxes as $name => $details) {
		if ($name == $tax)
			continue;
		?><label><input type="checkbox" name="taxes[]" value="<?php echo $name; ?>" /> <?php echo $details->label; ?></label> &emsp; <?php
	}
?>
</p>
	
<p><input type="button" class="button-secondary" value="<?php esc_attr_e('Check All', 'wptaxconvert'); ?>" onclick="this.value=check_all_rows()" />
<?php wp_nonce_field('import-taxconvert'); ?></p>
<ul style="list-style:none">

<?php	$hier = _get_term_hierarchy($tax);

		foreach ($this->all_terms[$tax] as $term) {
			$term = sanitize_term( $term, $tax, 'display' );

			if ( (int) $term->parent == 0 ) { ?>

	<li><label><input type="checkbox" name="terms_to_convert[]" value="<?php echo intval($term->term_id); ?>" /> <?php echo $term->name . ' (' . $term->count . ')'; ?></label><?php

				 if ( in_array( intval($term->term_id),  $this->hybrids_ids ) )
				 	echo ' <a href="#note"> * </a>';

				if ( isset($hier[$term->term_id]) )
					$this->_term_children($term, $hier, $tax); ?></li>
<?php		}
		} ?>
</ul>
<?php
	if ( ! empty($this->hybrids_ids) )
			echo '<p><a name="note"></a>' . __('* This term is already in another taxonomy, converting will add the new taxonomy term to existing posts in that taxonomy.', 'wptaxconvert') . '</p>'; ?>

<p class="submit"><input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('Go!', 'wptaxconvert'); ?>" /></p>
</form>

<?php }

	function _term_children($parent, $hier, $tax) { ?>

		<ul style="list-style:none; margin:0.5em 0 0 1.5em;">
<?php	foreach ($hier[$parent->term_id] as $child_id) {
			$child = get_term($child_id,$tax); ?>
		<li><label><input type="checkbox" name="terms_to_convert[]" value="<?php echo intval($child->term_id); ?>" /> <?php echo $child->name . ' (' . $child->count . ')'; ?></label><?php

			if ( in_array( intval($child->term_id), $this->hybrids_ids ) )
				echo ' <a href="#note"> * </a>';

			if ( isset($hier[$child->term_id]) )
				$this->_term_children($child, $hier, $tax); ?></li>
<?php	} ?>
		</ul><?php
	}

	function process($tax) {
		global $wpdb;

		if ( (!isset($_POST['terms_to_convert']) || !is_array($_POST['terms_to_convert'])) && empty($this->terms_to_convert) || (!isset($_POST['taxes'])) ) { ?>
			<div class="narrow">
			<p><?php printf(__('Uh, oh. Something didn&#8217;t work. Please <a href="%s">try again</a>.', 'wptaxconvert'), 'admin.php?import=wptaxconvert&amp;tax='.$tax); ?></p>
			</div>
<?php		return;
		}
		
		if ( empty($this->terms_to_convert) )
			$this->terms_to_convert = $_POST['terms_to_convert'];
		
		$taxonomy = $this->taxes[$tax];
		
		$convert = $_POST['convert'];
		if ($convert)
			$c_label = 'Convert';
		else
			$c_label = 'Copy';
			
		$new_taxes = $_POST['taxes'];
		$num = count($new_taxes);
			
		$hier = _get_term_hierarchy($tax);
		$hybrid_cats = $clear_parents = $parents = false;
		$clean_term_cache = array();
		$default_cat = get_option('default_category');

		echo '<ul>';

		foreach ( (array) $this->terms_to_convert as $term_id) {
			$term_id = (int) $term_id;
			$exists = term_exists($term_id,$tax);
			
			// check if the term exists in the current taxonomy (it always should!)
			if ( empty($exists) ) {
				echo '<li>' . sprintf( __('Term %s doesn&#8217;t exist in '.$taxonomy->label.'!', 'wptaxconvert'),  $term_id ) . "</li>\n";
			} else {
				// if the term exist do the copy/convert
				// $term is the existing term
				$term = get_term($term_id,$tax);
				echo '<li>' . sprintf(__($c_label.'ing term <strong>%s</strong> ... ', 'wptaxconvert'),  $term->name);

				// repeat process for each new taxonomy selected
				foreach ($new_taxes as $new_tax) {
					
					// check if the term is already in the new taxonomy & if not create it
					if ( ! ($id = term_exists( $term->slug, $new_tax ) ) )
						$id = wp_insert_term($term->name, $new_tax, array('slug' => $term->slug));
					
					// if the term couldn't be created return the error message
					if ( is_wp_error($id) ) {
						echo $id->get_error_message() . "</li>\n";
						continue;
					}
					
					// if the original term has posts, assign them to the new term
					$id = $id['term_taxonomy_id'];
					$posts = get_objects_in_term($term->term_id, $tax);
					$term_order = 0;

					foreach ( $posts as $post ) {
						$type = get_post_type($post);
						if (in_array($type, $taxonomy->object_type))
							$values[] = $wpdb->prepare( "(%d, %d, %d)", $post, $id, $term_order);
						clean_post_cache($post);
					}

					if ( $values ) {
						$wpdb->query("INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id, term_order) VALUES " . join(',', $values) . " ON DUPLICATE KEY UPDATE term_order = VALUES(term_order)");
						$wpdb->update($wpdb->term_taxonomy, array('count' => $term->count), array('term_id' => $term->term_id, 'taxonomy' => $new_tax) );
						
						echo __('Term added to posts.* ', 'wptaxconvert');
						
						if (!$convert)
							$hybrid_cats = true;
						$clean_term_cache[] = $term->term_id;
					}
	
	
					// Convert term
					if ($convert) {
						$del = wp_delete_term($term_id,$tax);
	
					// Set all parents to 0 (root-level) if their parent was the converted tag
						$wpdb->update($wpdb->term_taxonomy, array('parent' => 0), array('parent' => $term_id, 'taxonomy' => $tax) );
	
						if ( $parents ) $clear_parents = true;
						$clean_cat_cache[] = $term->term_id;
					}
					echo __($c_label.' successful.', 'wptaxconvert') . "</li>\n";
				}
			}
		}
		echo '</ul>';

		if ( ! empty($clean_term_cache) ) {
			$clean_term_cache = array_unique(array_values($clean_term_cache));
			clean_term_cache($clean_term_cache, $new_tax);
		}

		if ( $clear_parents ) delete_option('category_children');

		if ( $hybrid_cats )
			echo '<p>' . __('* This term is now in multiple taxonomies. The converter has added the new term to all posts with the original taxonomy term.', 'wptaxconvert') . '</p>';
		echo '<p>' . sprintf( __('We&#8217;re all done here, but you can always <a href="%s">convert more</a>.', 'wptaxconvert'), 'admin.php?import=wptaxconvert' ) . '</p>';
	}

	function init() {

		$tax = (isset($_GET['tax'])) ? $_GET['tax'] : 'category';
		$step = (isset($_GET['step'])) ? (int) $_GET['step'] : 1;

		$this->header($tax);

		if ( current_user_can('manage_categories') ) {
			if ($step == 1)
				$this->tabs($tax);
			elseif ($step == 2)
				$this->process($tax);
		}

		$this->footer();
	}

	function WP_Taxonomy_Converter() {
		// Do nothing.
	}
}

$wp_taxconvert = new WP_Taxonomy_Converter();

register_importer('wptaxconvert', __('Taxonomy Converter', 'wptaxconvert'), __('Convert or copy terms from one taxonomy to another.', 'wptaxconvert'), array(&$wp_taxconvert, 'init'));

} // class_exists( 'WP_Importer' )

function wptaxconvert_init() {
    load_plugin_textdomain( 'wptaxconvert', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'wptaxconvert_init' );
