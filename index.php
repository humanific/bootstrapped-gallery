<?php
/**
 * @package bootstrapped_gallery
 * @version 0.1
 */
/*
Plugin Name: Bootstrapped gallery
Plugin URI: https://github.com/humanific/bootstrapped-gallery
Description: Shortcode for displaying bootstrap blueimp-gallery galleries
Author: Francois Richir
Version: 0.1
Author URI: http://humanific.com
*/




function bootstrapped_gallery_shortcode($atts) {
   global $post, $bootstrapped_galleries_num;
   if(!$bootstrapped_galleries_num) $bootstrapped_galleries_num = 0;
   $bootstrapped_galleries_num++;
   wp_enqueue_style( 'blueimpgallery2', plugins_url( '/blueimp-gallery.min.css' , __FILE__ ));
   wp_enqueue_style( 'blueimpgallery', plugins_url( '/bootstrap-image-gallery.min.css', __FILE__ ));
   wp_enqueue_script( 'blueimpgallery1', plugins_url( '/jquery.blueimp-gallery.min.js' , __FILE__ ),array('bootstrap'));
   wp_enqueue_script( 'blueimpgallery', plugins_url( '/bootstrap-image-gallery.min.js' , __FILE__ ),array('blueimpgallery1'));
  
  
   if(isset($atts['ids'])){
      $pids = explode(',', $atts['ids']);
     $ids = array();
     foreach( $pids as $id ) $ids[] = intval($id);
      $wpq = new WP_Query( array(  'post__in' => $ids,'post_type' => 'attachment', 'post_status' => 'inherit','posts_per_page' => -1,'orderby' => 'post__in' ) );
    $images = $wpq->posts;
   }else if(isset($atts['postid'])){
    $images = get_children( array('post_parent' => $atts['postid'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') );
   }else{
      $images = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') );
   }

   $columns = !isset($atts['columns']) ? 6 : intval($atts['columns']);
   if( $columns == 6) $span =  "col-xs-4 col-md-2";
   if( $columns == 4) $span =  "col-xs-6 col-md-3";
   if( $columns == 3) $span =  "col-xs-6 col-sm-4";
   if( $columns == 2) $span =  "col-xs-6";
   if( $columns == 1) $span =  "col-xs-12";

  ob_start();
  if ($images) :?>
    <div class="gallery" id="bootstrapped-gallery-<?php echo $bootstrapped_galleries_num; ?>">
    <div class="row">
      <?php foreach( $images as $k => $imagePost ): 
        $image_attributes = wp_get_attachment_image_src(  $imagePost->ID, 'thumbnail' ); 
        $large = wp_get_attachment_image_src(  $imagePost->ID, 'large' ); 
        ?><div class="<?php echo $span ;?>">
        <a href="<?php echo $large[0]; ?>" data-toggle="tooltip" data-placement="top" class="thumbnail" data-gallery="bootstrapped-gallery-<?php echo $bootstrapped_galleries_num; ?>" title="<?php echo $imagePost->post_excerpt ?>" data-description="<?php echo $imagePost->post_content ?>">
        <img src="<?php echo $image_attributes[0]; ?>" class="fullwidth"/>
        </a>
        </div>
      <?php endforeach ;?>
      </div>
    </div>
    <?php if($bootstrapped_galleries_num==1):?>
<!-- The Bootstrap Image Gallery lightbox, should be a child element of the document body -->
<div id="blueimp-gallery" class="blueimp-gallery">
    <!-- The container for the modal slides -->
    <div class="slides"></div>
    <!-- Controls for the borderless lightbox -->
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
    <!-- The modal dialog, which will be used to wrap the lightbox content -->
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body next"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left prev">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                        Previous
                    </button>
                    <button type="button" class="btn btn-primary next">
                        Next
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/jscript">

 jQuery(function ($) {
  //$('[data-toggle="tooltip"]').tooltip();
  var borderless = true;
  $('#blueimp-gallery').data('useBootstrapModal', !borderless);
  $('#blueimp-gallery').toggleClass('blueimp-gallery-controls', borderless);
})
</script>
<?php endif ;?>
<?php 
add_action( 'wp_print_styles', 'add_gallery_styles', 100 );


endif; 
return ob_get_clean();
}


remove_shortcode('gallery', 'gallery_shortcode'); // removes the original shortcode
add_shortcode('gallery', 'bootstrapped_gallery_shortcode'); // add your own shortcode

?>