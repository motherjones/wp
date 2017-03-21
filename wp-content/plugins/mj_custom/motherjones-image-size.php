<?php
if ( !class_exists( 'MJ_Images' ) ) {

  class MJ_Images {

    private static $instance;
    public static function instance() {
      if ( ! isset( self::$instance ) ) {
        self::$instance = new MJ_Images;
        self::$instance->setup();
      }
      return self::$instance;
    }

    private $MJ_image_sizes = array(

      'full_width_giant' => array(
        'width' => 2400, 
        'height' => 1350, 
        'crop' => true,
        'name' => 'Full Width Title Image'
      ),

    );

    public function setup() {
      add_action( 'after_setup_theme', array($this, 'create_image_sizes') );
      add_filter( 'image_size_names_choose', array($this, 'set_image_selection') );
    }

    public function create_image_sizes() {
      add_theme_support('post-thumbnails');
      foreach ($this->MJ_image_sizes as $image => $image_size) {
        add_image_size($image, $image_size['width'], $image_size['height'], $image_size['crop']);
      }
    }

    public function set_image_selection( $sizes ) {
      $image_names = [];
      foreach ($this->MJ_image_sizes as $image => $image_size) {
        $image_names[$image] = $image_size['name'];
      }
      return array_merge( $sizes, $image_names );
    }

  }

  function MJ_Images() {
    return MJ_Images::instance();
  }
}

?>
