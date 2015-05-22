<?php 

/*==============================================
  Prevent Direct Access of this file
==============================================*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if this file is accessed directly

if ( !class_exists( 'TurboTabs_Setup' ) ) {
	class TurboTabs_Setup {	

	static $version = '1.0';	

 protected static function enqueue() {
 	add_action( 'wp_enqueue_scripts', array( 'TurboTabs_Setup', 'turbotabs_frontend_scripts' ) );
 	add_action( 'admin_enqueue_scripts', array( 'TurboTabs_Setup', 'turbotabs_admin_scripts' ) );
 	// fires groups variable to admin head
	add_action( 'admin_head', array( 'TurboTabs_Setup', 'collect_tab_groups' ) );
 }	//enqueue()	
 protected static function hooks() {
	//	Activation
	register_activation_hook( __FILE__,	array( 'TurboTabs_Setup', 'turbotabs_activate' ) );
	//	Deactivation
	register_deactivation_hook( __FILE__, array( 'TurboTabs_Setup', 'turbotabs_deactivate' ) );
	// unistall
	register_uninstall_hook( __FILE__, array( 'TurboTabs_Setup', ' turbotabs_unistall()' ) ); 
	// submit metabox change
	add_action( 'admin_menu', array( 'TurboTabs_Setup', 'turbotabs_replace_submit_meta_box' ) );
	// turbotabs custom columns
	add_filter( "manage_edit-turbotabs_columns", array( 'TurboTabs_Setup', "turbotabs_custom_columns" ) );
	add_action( "manage_turbotabs_posts_custom_column", array( 'TurboTabs_Setup', "turbotabs_custom_column_content" ), 10, 2 );
	//	TinyMCE Plugin
	add_filter( 'mce_external_plugins', array( 'TurboTabs_Setup', 'turbotabs_add_shortcode_plugin' ) );
	add_filter( 'mce_buttons', array( 'TurboTabs_Setup', 'turbotabs_register_shortcode_button' ) );
 }	//hooks()	
	/*================================================
	   flush rewrite rules &&
	   activation/deactivation hooks
	=================================================*/
	 public static function turbotabs_activate(){
	    flush_rewrite_rules();
	 }
	 public static function turbotabs_deactivate(){
	    flush_rewrite_rules();
	 }
	 public static function turbotabs_unistall(){
	 	  /** Delete turbotabs_group Taxonomiy */
	 	  global $wpdb;
		  $taxonomy = 'turbotabs_group'; 
		  // Prepare & excecute SQL
		  $terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy ) );
		  
		         // Delete Terms
		  if ( $terms ) {
		  	foreach ( $terms as $term ) {
		  		$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
		  		$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) );
		  		delete_option( 'prefix_' . $taxonomy->slug . '_option_name' );
		  	}
		  }
			
			// Delete Taxonomy
			$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
	 }
	/*========================================
				 ENQUEUE SCRIPTS
	=========================================*/
	public static function turbotabs_frontend_scripts(){
	   // styles
   	 wp_enqueue_style( 'animations-css', plugin_dir_url( __FILE__ ) . '../assets/css/animate.min.css' );
   	 wp_enqueue_style( 'font-awesome', plugin_dir_url( __FILE__ ) . '../assets/vendor/css/font-awesome.min.css' );
 	 wp_enqueue_style( 'turbotabs-css', plugin_dir_url( __FILE__ ) . '../assets/css/turbotabs.css', self::$version );
 	 // script
 	 wp_enqueue_script('turbotabs', plugins_url('../assets/js/turbotabs.js', __FILE__), array('jquery'), self::$version , true);  
	} //turbotabs_frontend_scripts()
	public static function turbotabs_admin_scripts(){
		  wp_register_script( 'turbotabs-preview-js', plugin_dir_url( __FILE__ ) . '../assets/js/preview.min.js', true );
		  wp_register_script( 'color-picker', plugin_dir_url( __FILE__ ) . '../assets/js/spectrum.min.js', true );
		  wp_register_script( 'turbotabs-admin-js', plugin_dir_url( __FILE__ ) . '../assets/js/admin.js', true );
		  wp_register_style( 'turbotabs-admin-css', plugin_dir_url( __FILE__ ) . '../assets/css/admin.css' );
		  wp_register_style( 'font-awesome', plugin_dir_url( __FILE__ ) . '../assets/vendor/css/font-awesome.min.css' );

		  // check if on admin page, then enqueue
		  if( is_admin() ){
		    wp_enqueue_script('turbotabs-preview-js');
		    wp_enqueue_script('color-picker');
		    wp_enqueue_script('turbotabs-admin-js');
		    wp_enqueue_style('turbotabs-admin-css'); 
		    wp_enqueue_style('font-awesome');
		  } //if
	} //turbotabs_admin_scripts()
	/*================================================
				Add tinyMCE Button
	=================================================*/
	public static function turbotabs_register_shortcode_button( $buttons ) {
		 global $current_screen;
		 $type = $current_screen->post_type;

		 if ( is_admin() && ($type == 'post' || $type == 'page') ) {
			array_push( $buttons, "turbotabs" );
		 }	

		return $buttons;
	} //turbotabs_register_shortcode_button()
	public static function turbotabs_add_shortcode_plugin( $plugin_array ) {
		 global $current_screen;
		 $type = $current_screen->post_type;

		 if ( is_admin() && ($type == 'post' || $type == 'page') ) {
		 	$plugin_array['turbotabs'] = plugin_dir_url( __FILE__ )  . '../assets/js/shortcode.js';
		 }
		 return $plugin_array;
	} //turbotabs_add_shortcode_plugin()

	/*==============================================
		Replace Default Publish Meta Box
	===============================================*/	
	public static function turbotabs_replace_submit_meta_box() {
	    remove_meta_box('submitdiv', 'turbotabs', 'core');
	    add_meta_box('submitdiv', __('Save/Update Tab'), array( 'TurboTabs_Setup', 'turbotabs_submit_meta_box' ), 'turbotabs', 'side', 'low');
	}
	// custom edit of default wordpress publish box 
	// added from wordpress/includes/metaboxes.php
	public static function turbotabs_submit_meta_box() {
	  global $action, $post;
	 
	  $post_type = $post->post_type;
	  $post_type_object = get_post_type_object($post_type);
	  $can_publish = current_user_can($post_type_object->cap->publish_posts);
	  ?>
	  <div class="submitbox" id="submitpost">
	   <div id="major-publishing-actions">
	   <?php
	   do_action( 'post_submitbox_start' );
	   ?>
	   <div id="delete-action">
	   <?php
	   if ( current_user_can( "delete_post", $post->ID ) ) {
	     if ( !EMPTY_TRASH_DAYS )
	          $delete_text = __('Delete Permanently');
	     else
	          $delete_text = __('Move to Trash');
	   ?>
	   <a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo $delete_text; ?></a><?php
	   } //if ?>
	  </div>
	   <div id="publishing-action">
	   <span class="spinner"></span>
	   <?php
	   if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
	        if ( $can_publish ) : ?>
	          <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Add Tab') ?>" />
	          <?php submit_button( __( 'Add Tab' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'p' ) ); ?>
	   <?php   
	        endif; 
	   } else { ?>
	          <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update Tab') ?>" />
	          <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php esc_attr_e('Update Tab') ?>" />
	   <?php
	   } //if ?>
	   </div>
	   <div class="clear"></div>
	   </div>
	   </div>
	  <?php
	  } //turbotabs_submit_meta_box()
	/*============================================
	*		Add groups Array to Admin Head
	*       ==============================
	*
	*  Collect turbotabs_group custom taxonomy
	*  then loop through them asigning $group->slug
	*  as the key of the javascript array, and
	*  $group->name and $group->count as the values
	*  that will be used later in shortcode.
	*
	*=============================================*/
	public static function collect_tab_groups(){
	  $groups = get_terms('turbotabs_group');
	  ?>
	  <script type="text/javascript">
	    var groups = {}; 
	    <?php foreach( $groups as $group ): ?>
	    groups['<?php echo $group->slug ?>'] = ['<?php echo $group->name; ?>', <?php echo $group->count; ?>];
	    <?php endforeach; ?>  
	  </script>
	  <?php 
	 } //collect_tab_groups()
	/*=========================================
			TurboTabs Post Custom Columns
	==========================================*/
	public static function turbotabs_custom_columns( $cols ) {
		$cols = array( 'cb' => '<input type="checkbox" />',
		  'title' => __( 'Tab Navigation Title', 'turbotabs' ),
		  'subtitle' => __('SubTitle','turbotabs'),
		  'icon' => __( 'Icon', 'turbotabs' ),
		  'group' => __('Group', 'turbotabs'),
		  'date' => __('Date', 'turbotabs')
		); 
		return $cols;
	} // turbotabs_custom_columns()
	// now add custom content
	public static function turbotabs_custom_column_content( $column, $post_id ) {
	  switch ( $column ) {
	  	case 'subtitle':
	  	$subtitle = get_post_meta($post_id, 'turbotab_sub', true);
	  	if( $subtitle ){
	  		echo '<span class="clm-sub">'. $subtitle .'</span>';
	  	}
	  	break;
	    case "icon":
	     $icon = get_post_meta( $post_id, 'turbotab_icon', true );
	     if( $icon ) {
	       echo '<span class="fa ' . $icon . ' fa-2x"></span>';
	     }
	    break;
	    case 'group':
	    echo get_the_term_list( $post_id, 'turbotabs_group');
	    break;
	  }
	} //turbotabs_custom_column_content()
	/*=====================================
			   Initializing
	=====================================*/
	public static function initialize(){
		self::enqueue();
		self::hooks();
	} //initialize()
  }	// if class !exists
}  //TurboTabs_Setup
?>