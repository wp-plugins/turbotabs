<?php 

/*==============================================
  Prevent Direct Access of this file
==============================================*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if this file is accessed directly

if ( !class_exists( 'TurboTabs_Shortcode' ) ) {
   class TurboTabs_Shortcode {

   protected static function turbotabs_hook(){
   	 add_shortcode( 'turbotabs', array( 'TurboTabs_Shortcode', 'turbotabs') );
   } //turbotabs_hook()
   /*==========================================================
					   Generating Shortcode
						==================

	  Collecting  all  turbotabs (custom post type)  that
	  belongs to  the  assigned  $group  (turbotabs_group)	
	  that is selected from  tinyMCE editor  via  shortcode
	  button. Then collect all the options (term_meta values)
	  that are selected for that particular group, which will
	  be further extracted to the hidden <input> field from
	  where jQuery will  harvest them  and  do  the  magic.

   ============================================================*/
   public static function turbotabs( $atts ){

   	  extract( shortcode_atts( array(
   	  		'group'  => ''
   	   ), $atts ) );

      if( $group ){

       	// collect all tabs
       	$arg = array(
       			'post_type' => 'turbotabs',
            'posts_per_page' => 3,
       			'tax_query' => array(
       				array(
       					'taxonomy' => 'turbotabs_group',
       					'field'    => 'slug', 
       					'terms'	   => $group	
       				)
       			)
       	);

     	  $tabs = get_posts($arg);

     	  // gather chosen options
     	  $term = get_term_by('slug', $group, 'turbotabs_group');
     	  $t_id = $term->term_id;
    	  $term_meta = get_option( "taxonomy_$t_id" );

    	  // getting the values
    	  $animation 	  = $term_meta['animation'];
    	  $layout 	 	  = $term_meta['layout'];
    	  $index		    = $term_meta['index'];
    	  $mode 	 	    = $term_meta['mode'];
        $force_height = $term_meta['force_height'];
    	  $align_h 	 	  = $term_meta['align_h'];
    	  $align_v 	    = $term_meta['align_v'];
    	  $shadow 	 	  = $term_meta['shadow'];	
    	  $shadow_shade = $term_meta['shadow_shade'];
    	  $navigation_color = $term_meta['navigation_color'];
    	  $content_color    = $term_meta['content_color'];
        $title_color  = $term_meta['title_color'];
    	  $text_color   = $term_meta['text_color']; 
        $icon_color   = $term_meta['icon_color'];
    	  $heading_bck 	= $term_meta['heading_bck'];
    	  $hover_bck    = $term_meta['hover_bck'];
    	  $active_bck 	= $term_meta['active_bck'];
    	  $border_color = $term_meta['border_color'];
    	  $width_s 		  = $term_meta['width_s'];
    	  $width_p 		  = $term_meta['width_p'];
    	  $padd_p 		  = $term_meta['padd_p'];
    	  $padd_s 		  = $term_meta['padd_s'];
    	  $shadow_on 	  = 'off';
    	  $width 	 	    = '';
    	  $padding 	 	  = '';
    	  $position 	  = '';
    	  $align 		    = '';

        if( $shadow === 'no' ){
              $shadow_shade = '';
         } else {
              $shadow_on = 'on';
        }

    	  if( '' != $width_p || 0 != $width_p ){
    	  	$width = $width_p . 'px';
    	  } else {
    	  	$width = $width_s . '%';
    	  }
    	  if( '' != $padd_p || 0 != $padd_p ){
    	  	$padding = $padd_p . 'px';
    	  } else {
    	  	$padding = $padd_s . '%';
    	  }
    	  if( $mode === 'horizontal' ){
    	  	$position = $position_tb;
    	  	$align = $align_h;
    	  } else if( $mode === 'vertical' ){
    	  	$position = $position_lr;
    	  	$align = $align_v;
    	  }

    	  //outputing the content
    	  $output = '<div id="turbotabs_' . $t_id . '" class="turbotabs ' . '" data-tt-animation="'. $animation .'" data-tt-align="'. $align .'" data-tt-width="'.$width.'" data-tt-padding="'.$padding.'" data-tt-mode="'.$mode.'" data-tt-index="'. $index .'">';
    	  $output .= '<ul class="tt_tabs">';
    	  foreach( $tabs as $tab ){
    	  	setup_postdata($tab);
    	  	$icon = get_post_meta($tab->ID, 'turbotab_icon', true);
          $subtitle = get_post_meta( $tab->ID, 'turbotab_sub', true );
    	  	$output .= '<li><span class="nav-hld">' . ( $icon ? '<i class="fa ' . $icon . '"></i>'  : '' ) . '<span class="ttnav-title">' . apply_filters('the_title', $tab->post_title) . '</span></span><span class="ttnav-sbt">'. $subtitle .'</span></li>';
    	  }
    	  $output .= '</ul><div class="tt_container" style="color: '. $text_color .'; background: '. $content_color .'">';
    	  foreach( $tabs as $tab ){
    	  	setup_postdata($tab);
    	  	$output .= '<div class="tt_tab">' . apply_filters('the_content', $tab->post_content) . '</div>';
    	  }
    	  $output .= '</div>';
    	  $output .= '<input type="hidden" id="turbotabs_styles" data-tt-layout="' . $layout . '" data-tt-shd="'. $shadow_on .'" data-tt-shdc="'. $shadow_shade .'"  data-tt-bdc="'. $border_color .'" data-tt-nc="'. $navigation_color .'" data-tt-tc="'. $text_color .'" data-tt-ttl="'. $title_color .'" data-tt-ic="'. $icon_color .'" data-tt-cc="'. $content_color .'" data-tt-fh="'. $force_height .'" data-tt-hbck="'. $heading_bck .'" data-tt-hb="'. $hover_bck .'" data-tt-ab="'. $active_bck .'" />';
    	  $output .= '<div class="tt_overlay"></div></div>';
    	  wp_reset_postdata();
    	  
  	    return $output;
     } else {
          return;
     }
   }//turbotabs() 
	/*=========================================
				Initialize
	==========================================*/
    public static function initialize(){
		self::turbotabs_hook();
	}

  }	// if class !exists
}  //TurboTabs_Shortcode
?>