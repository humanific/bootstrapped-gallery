<?php
/**
 * @package bootstrapped_gallery
 * @version 0.01
 */
/*
Plugin Name: Bootstrapped gallery
Plugin URI: https://github.com/humanific/bootstrapped-gallery
Description: Shortcode for displaying bootstrap blueimp-gallery galleries
Author: Francois Richir
Version: 0.01
Author URI: http://humanific.com
*/




function bootstrapped_gallery_shortcode($atts) {
   global $post, $bootstrapped_galleries_num;
   if(!$bootstrapped_galleries_num) $bootstrapped_galleries_num = 0;
   $bootstrapped_galleries_num++;

  
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
    <div class="gallery" id="gallery<?php echo $bootstrapped_galleries_num; ?>">
    <div class="row">
      <?php foreach( $images as $k => $imagePost ): 
        
        $image_attributes = wp_get_attachment_image_src(  $imagePost->ID, 'thumbnail' ); 
        $large = wp_get_attachment_image_src(  $imagePost->ID, 'large' ); 
        $url = $atts['lightbox'] != 'false' ? $large[0] : get_attachment_link(  $imagePost->ID );
        ?><div class="<?php echo $span ;?>">
        <a href="<?php echo $url; ?>" data-toggle="tooltip<?php echo $bootstrapped_galleries_num; ?>" data-placement="top" class="thumbnail" data-gallery="bootstrapped-gallery-<?php echo $bootstrapped_galleries_num; ?>" data-title="<?php echo $imagePost->post_excerpt ?>"  title="<?php echo $imagePost->post_excerpt ?>" >
        <img src="<?php echo $image_attributes[0]; ?>" class="fullwidth"/>
        </a>
        </div>
      <?php endforeach ;?>
      </div>
    </div>
<?php if($bootstrapped_galleries_num==1):?>
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <p class="description"></p>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>

<?php endif;?> 
<script>
 jQuery(document).ready(function ($) {
  <?php if($atts['tooltip'] == 'true') :?> $('[data-toggle="tooltip<?php echo $bootstrapped_galleries_num; ?>"]').tooltip(); <?php endif;?> 
  <?php if($atts['lightbox'] != 'false') :?> 
  $('#gallery<?php echo $bootstrapped_galleries_num; ?>').click(function (event) {
  event = event || window.event;
    var target = event.target || event.srcElement,
        link = target.src ? target.parentNode : target;
    blueimp.Gallery($(this).find('a'), {
      index: link, 
      event: event, 
      titleProperty: 'data-title',
      onslide: function (index, slide) {
        $(this.container).find('.title').html($(this.list[index]).attr('data-title'));
        }
    });
    return false;
  });
  <?php endif ;?>
})
</script>

<?php 
add_action( 'wp_print_styles', 'add_gallery_styles', 100 );
endif; 
return ob_get_clean();
}


remove_shortcode('gallery', 'gallery_shortcode');
add_shortcode('gallery', 'bootstrapped_gallery_shortcode');



function bootstrapped_gallery_scripts() {
   wp_enqueue_script( 'blueimp-gallery', plugins_url( '/js/blueimp-gallery.min.js' , __FILE__ ),array('jquery'));
}


add_action( 'wp_enqueue_scripts', 'bootstrapped_gallery_scripts' );

function bootstrapped_gallery_styles() {
  wp_enqueue_style( 'blueimp-gallery', plugins_url( '/css/blueimp-gallery.css' , __FILE__ ));
}


add_action( 'wp_print_styles', 'bootstrapped_gallery_styles', 100 );
?>