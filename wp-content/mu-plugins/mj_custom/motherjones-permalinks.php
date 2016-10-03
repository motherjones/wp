<?php

if ( !class_exists( 'MJ_Permalinks' ) ) {
  class MJ_Permalinks {

    private static $instance;
    public static function instance() {
      if ( ! isset( self::$instance ) ) {
        self::$instance = new MJ_Permalinks;
        self::$instance->setup();
      }
      return self::$instance;
    }

    public function setup() {
			add_filter('post_type_link', array($this, 'permalink_rewrite'), 10, 3);   
      add_filter( 'request', array($this, 'alter_the_query') );
    }


    public function alter_the_query( $request ) {
        $dummy_query = new WP_Query();  // the query isn't run if we don't pass any query vars
        $dummy_query->parse_query( $request );

        // this is the actual manipulation; do whatever you need here
        if ($dummy_query->query['category_name'] && $dummy_query->query['name']) { //is article type
          $request['post_type'] = array('mj_article', 'mj_full_width');
          if (get_terms( array( // is blog post
              'slug' => $dummy_query->query['category_name'],
              'taxonomy' => 'mj_blog_type'
          ) ) ) {
            $request['post_type'] = 'mj_blog_post';
            $request['tax_query'] = array( array(
              'taxonomy' => 'mj_blog_type',
              'field' => 'slug',
              'terms' => $request['category_name'],
            ) );
            unset($request['category_name']);
          }
        } elseif ( preg_match('/^author\//', $dummy_query->query['category_name']) ) { //is author
          print_r($request);
          //$request['post_type'] = array('mj_article', 'mj_full_width', 'mj_blog_post');

          $request['author_name'] = str_replace ('author/', '', $dummy_query->query['category_name']);
          unset($request['category_name']);
        }  elseif ( //is topic
          !get_terms( array(
            'taxonomy' => 'category', 
            'slug' => $request['category_name']) 
          ) &&
          get_terms( array(
            'taxonomy' => 'mj_primary_tag', 
            'slug' => $request['category_name']) 
          ) ) {
            $request['post_type'] = array('mj_article', 'mj_full_width', 'mj_blog_post');
            $request['tax_query'] = array( array(
              'taxonomy' => 'mj_primary_tag',
              'field' => 'slug',
              'terms' => $request['category_name'],
            ) );
            unset($request['category_name']);
        }  elseif ( //is media type
          !get_terms( array(
            'taxonomy' => 'category', 
            'slug' => $request['category_name']) 
          ) &&
          get_terms( array(
            'taxonomy' => 'mj_media_type', 
            'slug' => $request['category_name']) 
          ) ) {
            $request['post_type'] = array('mj_article', 'mj_full_width', 'mj_blog_post');
            $request['tax_query'] = array( array(
              'taxonomy' => 'mj_media_type',
              'field' => 'slug',
              'terms' => $request['category_name'],
            ) );
            unset($request['category_name']);
        }  elseif ( //is blog posts
          !get_terms( array(
            'taxonomy' => 'category', 
            'slug' => $request['category_name']) 
          ) &&
          get_terms( array(
            'taxonomy' => 'mj_blog_type', 
            'slug' => $request['category_name']) 
          ) ) {
            $request['post_type'] = array('mj_blog_post');
            $request['tax_query'] = array( array(
              'taxonomy' => 'mj_blog_type',
              'field' => 'slug',
              'terms' => $request['category_name'],
            ) );
            unset($request['category_name']);
        }
        return $request;
    }

			// Adapted from get_permalink function in wp-includes/link-template.php
		public function permalink_rewrite($permalink, $post_id, $leavename) {
			$post = get_post($post_id);
			$rewritecode = array(
				'%year%',
				'%monthnum%',
				'%day%',
				'%hour%',
				'%minute%',
				'%second%',
				$leavename? '' : '%postname%',
				'%post_id%',
				'%category%',
				'%mj_blog_type%',
				'%author%',
				$leavename? '' : '%pagename%',
			);

			if ( '' != $permalink && !in_array($post->post_status, array('draft', 'pending', 'auto-draft')) ) {
				$unixtime = strtotime($post->post_date);

				$category = '';
				if ( strpos($permalink, '%category%') !== false ) {
					$cats = get_the_category($post->ID);
					if ( $cats ) {
						usort($cats, '_usort_terms_by_ID'); // order by ID
						$category = $cats[0]->slug;
						if ( $parent = $cats[0]->parent )
							$category = get_category_parents($parent, false, '/', true) . $category;
					}
					// show default category in permalinks, without
					// having to assign it explicitly
					if ( empty($category) ) {
						$default_category = get_category( get_option( 'default_category' ) );
						$category = is_wp_error( $default_category ) ? '' : $default_category->slug;
					}
				}
        if (wp_get_post_terms( $post->ID, 'mj_blog_type' )) {
          $mj_blog_type = wp_get_post_terms( $post->ID, 'mj_blog_type' )[0]->slug;
        }

				$author = '';
				if ( strpos($permalink, '%author%') !== false ) {
					$authordata = get_userdata($post->post_author);
					$author = $authordata->user_nicename;
				}

				$date = explode(" ",date('Y m d H i s', $unixtime));
				$rewritereplace =
					array(
						$date[0],
						$date[1],
						$date[2],
						$date[3],
						$date[4],
						$date[5],
						$post->post_name,
						$post->ID,
						$category,
            $mj_blog_type,
						$author,
						$post->post_name,
					);
				$permalink = str_replace($rewritecode, $rewritereplace, $permalink);
			} else { // if they're not using the fancy permalink option
			}
			return $permalink;
		}

  }

  function MJ_Permalinks() {
    return MJ_Permalinks::instance();
  }
}
?>
