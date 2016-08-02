<?php
if ( !class_exists( 'MJ_Images' ) ) {

  class MJ_Images {

    private static $instance;
    public static function instance() {
      if ( ! isset( self::$instance ) ) {
        self::$instance = new MJ_Taxonomy;
        self::$instance->setup();
      }
      return self::$instance;
    }

    private $MJ_image_sizes = array(

      'article_top' => array(
        'width' => 630, 
        'height' => 354, 
        'crop' => true
      ),

      'index_thumb' => array(
        'width' => 208, 
        'height' => 117, 
        'crop' => true
      ),

      'large_990' => array(
        'width' => 990, 
        'height' => 557, 
        'crop' => true
      ),

      'homepage_top_story' => array(
        'width' => 800, 
        'height' => 450, 
        'crop' => true
      ),

      'homepage_section_thumb' => array(
        'width' => 161, 
        'height' => 91, 
        'crop' => true
      ),

      'homepage_investigations' => array(
        'width' => 485, 
        'height' => 273, 
        'crop' => true
      ),
    );

    public function setup() {
      global $_wp_additional_image_sizes;

      foreach ($MJ_image_sizes as $image => $image_size) {
        $_wp_additional_image_sizes[ $image ] = $image_size;
      }
    }

  }

  function MJ_Images() {
    return MJ_Images::instance();
  }
}

?>
