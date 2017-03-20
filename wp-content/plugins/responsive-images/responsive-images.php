<?php
/*
Plugin Name: Responsive Images
Description: Take a different image adapted to each kind of dispositive to have a better responsive system in your page.
Version: 1.0.0
Author: Pedro Escudero
Author URI: http://es.linkedin.com/in/pedroescuderozumel/es
Plugin URI: http://github.com/responsive-images
License: GPL1
*/

/*
  I try to program as the next one that read it could be a dangerous psycho who knows where I live
  If you have any suggestion about the code, please let me know
*/


// Check for existing class
if ( ! class_exists( 'responsive_images' ) ) {
/**
	 * Main Class
	 */
	class responsive_images  {

		/**
		 * Class constructor: initializes class variables and adds actions and filters.
		 */
		public function __construct() {
			$this->responsive_images();
		}

		public function responsive_images() {
			register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
			register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivation' ) );

			// Register admin only hooks
			if(is_admin()) {
				$this->register_admin_hooks();
			}
                        
                        // Register global hooks
			$this->register_global_hooks();
		}
                /**
		 * Registers global hooks.
		 */
		public function register_global_hooks() {
			
      add_action( 'the_content', array($this,'change_to_responsive_image') );
      

		} 
   
    /**
     * Registers admin only hooks.
     */
    public function register_admin_hooks() {
      
      // Add Settings Link
      add_action('admin_menu', array($this, 'admin_menu'));

      // Add settings link to plugins listing page
      add_filter('plugin_action_links', array($this, 'plugin_settings_link'), 2, 2);

      
    }
     
                
	                  
		/**
		 * Handles activation tasks, such as registering the uninstall hook.
		 */
		public function activation() {
			register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
                       
                        
		}
               
		/**
		 * Handles deactivation tasks, such as deleting plugin options.
		 */
		public function deactivation() {

		}

		/**
		 * Handles uninstallation tasks, such as deleting plugin options.
		 */
		public function uninstall() {
			
		}

    
		/**
		 * Admin: add settings link to plugin management page
		 */
		public function plugin_settings_link($actions, $file) {
			if(false !== strpos($file, 'responsive-images')) {
				$actions['settings'] = '<a href="options-general.php?page=responsiveimages">Settings</a>';
			}
			return $actions;
		}

		/**
		 * Admin: add Link to sidebar admin menu
		 */
		public function admin_menu() {
			
			add_options_page('Responsive Images', 'Responsive Images', 'manage_options', 'responsiveimages', array($this, 'settings_page'));
		  add_option('mobile_image', "thumb");
  
    }
                        
		/**
		 * Admin: settings page
		 */
		public function settings_page() {
			if ( !current_user_can( 'manage_options' ) )  {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			} 
       if( isset($_POST['hidden_flag'])) {

           $value_mobile_image = $_POST['mobile_image'];
         
           echo "done!".$value_tablet_image;
           update_option( 'mobile_image', $value_mobile_image );
         
         }else{
             $value_mobile_image = get_option ('mobile_image');
                
         }
           switch( $value_mobile_image){
              case 'thumb':
                $option_mobile_select_thumb = "selected";
              break;
              case 'medium':
                $option_mobile_select_medium = "selected";
              break;
              case 'large':
                $option_mobile_select_large = "selected";
              break;
           }

           

      ?>
                          
                            
			<div class="wrap">

				<?php screen_icon(); ?>

				<h2>Responsive Images</h2>

				<hr/>

			
                                
				<h2>Description</h2>
				<p>
				 This plugin detects the kind of dispositive os the user that is accessing to your website and load different size of images if it is a mobile device.
				</p>
                                <p>
                                    If this plugin has been useful, you may see my professional profile in <a href="http://es.linkedin.com/in/pedroescuderozumel/es" target="_blank">Linkedin</a> or follow me work at <a target="_blank" href="https://github.com/PedroEscudero">github</a>. Do you have any suggestion about this plugin? Please <a href="mailto:pedroescudero.zumel@gmail.com">write me</a>.
                                </p>
        <hr/>
        
        <form name="form_image" method="post" action="">

          <p>
            <input type='hidden' name='hidden_flag' value='1' />
            <label>Size of mobile image: </label>
            <select name='mobile_image'>
                <option value='thumbnail' <? echo  $option_mobile_select_thumb; ?>>Thumbnail</option>
                <option value='medium' <? echo $option_mobile_select_medium; ?>>Medium</option>
                <option value='large' <? echo  $option_mobile_select_large; ?>>Large</option>
            </select>        
         
         
          </p>
          <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
          </p>                        
				</form>
                              
                                
			 	<hr/>
                                
			</div>
			<?php
		}
    public function change_to_responsive_image() {
    
      $is_mobile = $this -> search_dipositive();
      $content = get_the_content();
      $content = wpautop(wptexturize($content));
      if ($is_mobile == 1 )
      {        
       
        $post_id = get_the_id();
          if ( preg_match_all('/<img (.+?)>/', $content, $matches) ) {
            $args = array( 'post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' =>'any', 'post_parent' => $post_id, 'order'=> 'ASC', 'orderby' => 'ID');
            $list_attachment = get_posts($args);
            foreach ($matches[1] as $match) {
                        foreach ( wp_kses_hair($match, array('http')) as $attr){
                            $img[$attr['name']] = $attr['value'];
                          }
                          $kind_of_image = get_option('mobile_image');
                         
                          $url_mini = wp_get_attachment_image_src ( $list_attachment[0]->ID , 'thumbnail');
                          $content = str_replace ( $img['src'] , $url_mini[0]  , $content);
                          array_splice ( $list_attachment, 0, 1);
               }
        }
    
      }
       return $content;
    }

    function first_post_image( $post ) {
      
      $first_image = '';
      ob_start();
      ob_end_clean();
      $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
      $first_image = $matches [1] [0];
      $url = site_url("/wp-content/plugins/responsive-images/assets/");
      
      if(empty( $first_image )){ 
        $first_image = $url . "none.jpg";
      }
      return $first_image;
  }

    public function search_dipositive(){
    
            $is_mobile = 0;

            // Search user agent
            if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i',strtolower($_SERVER['HTTP_USER_AGENT']))){
                $is_mobile = 1;
            }

            //search MIME
            
            if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or
                ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))){
                $is_mobile = 1;
            }

            $mobile_user_agent = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
            $mobile_agents = array(
                'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
                'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
                'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
                'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
                'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
                'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
                'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
                'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
                'wapr','webc','winw','winw','xda','xda-');

            // Search for agents at mobile array 
            if(in_array($mobile_user_agent,$mobile_agents)){
                $is_mobile = 1;
            }

            //$_SERVER['ALL_HTTP'] -> Todas las cabeceras HTTP
            
            if(strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
                $is_mobile = 1;
            }
            if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
                $is_mobile = 0;
            }

            return $is_mobile; // 0 = standard, 1 = mobile
}
                public function get_entry_author( $author_id ) {
                    global $table_prefix;
                    global $wpdb;
                    $table = $table_prefix . "users";
                    $consulta = "SELECT display_name FROM $table WHERE ID ='{$author_id}' ";
                    $resultado = $wpdb->get_results( $consulta );
                    return $resultado[0] ->display_name;
                }
                  
	} // End social_fellow class

	// Init Class
	new responsive_images();
}

?>
