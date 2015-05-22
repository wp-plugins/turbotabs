<?php 
  /*==============================================
   Prevent Direct Access of this file
  ==============================================*/
  if ( ! defined( 'ABSPATH' ) ) exit; // Exit if this file is accessed directly

  /*==============================================
      Register TurboTabs custom post type
  ==============================================*/
  function turbotabs_registration(){

    register_post_type( 'turbotabs', array( 

            'labels' => array(
              'name'    => __('TurboTabs', 'turbotabs'),
              'singular_name'   => __('Tab', 'turbotabs'),
              'plural_name'   => __('Tabs', 'turbotabs'),
              'add_new'     => __('Add Tab', 'turbotabs'),
              'add_new_item'    => __('Add new Tab', 'turbotabs'),
              'new_item'      => __('New Tab', 'turbotabs'),
              'edit_item'     => __('Edit Tab', 'turbotabs'),
              'all_items'     => __('All Tabs', 'turbotabs'),
              'view_item'     => __('View Tab', 'turbotabs'),
              'not_found'     => __('No Tab found'),
              'not_found_in_trash'  => __('No Tabs found in trash', 'turbotabs'),
            ),
            'public'    => true,
            'rewrite'   => array( 'slug' => 'Tab' ),
            'menu_icone'  => '',
            'supports'    => array( 'title', 'editor', 'page-attributes' ),
            'menu_position' => 65,
            'menu_icon'     => plugin_dir_url( __FILE__ ) . '../assets/images/turbotabs_icon.png' 
        )
    );
  }
  add_action( 'init', 'turbotabs_registration' );
  /*==============================================
              Add groups to Tabs 
          (register custom taxonomy)
  ==============================================*/
  function turbotabs_group(){

    register_taxonomy( 'turbotabs_group' ,'turbotabs', array(
            'labels'  => array(
              'name'         => __('Groups', 'turbotabs'),
              'singular_name'    => __('Group', 'turbotabs'),
              'search_items'     => __( 'Search Groups', 'turbotabs' ),
              'all_items'      => __( 'All Groups', 'turbotabs' ),
              'edit_item'      => __( 'Edit Group', 'turbotabs' ),
              'update_item'    => __( 'Update Group', 'turbotabs' ),
              'add_new_item'     => __( 'Add New Group', 'turbotabs' ),
              'new_item_name'    => __( 'New Group Name', 'turbotabs' ),
              'menu_name'      => __( 'Groups', 'turbotabs' ),
              'not_found'      => __('No Group found', 'turbotabs')
             ),
            'rewrite'    => array('slug' => 'group'),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'public'   => true,
            'query_var'         => true
      )
    );
  }
  add_action( 'init', 'turbotabs_group' );
  /*========================================================
            Add Icon Field for TurboTabs post_type
  =========================================================*/
  function turbotabs_meta(){
    add_meta_box( 'Icon', 
        __('Nav Title Icon', 'turbotabs'), 
        'tab_icon_meta_box',
        'turbotabs',
        'side',
        'high'
    );
    add_meta_box( 'sub', 
        __('Optional Navigation Sub-Title', 'turbotabs'), 
        'tab_sub_meta_box',
        'turbotabs',
        'advanced',
        'high'
    );
  }
  add_action( 'add_meta_boxes', 'turbotabs_meta' ); 

  //add thumbnails meta box callback
  function tab_icon_meta_box($post){

    $font_awesome = get_post_meta( $post->ID, 'turbotab_icon', true );
    $icons_list   = turbotabs_font_awesome();
    ?>
    <p>
    <div class="font-aws">
     <select name="turbotab_icon" id="turbotab_icon" style="font-family: 'FontAwesome', Helvetica; font-size: 1.5em">
       <option><?php _e('Select an Icon', 'turbotabs'); ?></option>
       <?php foreach( $icons_list as $icon => $value ): ?>
       <option value="<?php echo $icon; ?>"<?php echo ( $icon == $font_awesome ) ? 'selected' : ''; ?>><?php echo '&#x'. stripslashes($value) . ' '. $icon; ?></option>
     <?php endforeach; ?>
    </select><br/>  
    </div>
    </p>
  <?php
  }
  // add optional subtitle
  function tab_sub_meta_box($post){
    $tab_sub = get_post_meta( $post->ID, 'turbotab_sub', true );
    ?>
    <p>
      <span class="pro">Available in PRO Version</span>
    </p>
  <?php
  }
  // moving tab subtitle metabox below title
  // source: http://wordpress.stackexchange.com/questions/36600/how-can-i-put-a-custom-meta-box-above-the-editor-but-below-the-title-section-on
  add_action('edit_form_after_title', function() {
      global $post, $wp_meta_boxes, $current_screen;
      $type = $current_screen->post_type;
      if( $type === 'turbotabs' ){
      do_meta_boxes(get_current_screen(), 'advanced', $post);
      unset($wp_meta_boxes[get_post_type($post)]['advanced']);
      }
  });
  // add help text to turbotab post
   function wptutsplus_text_after_title( $post_type ) { ?>
    <div class="after-title-help postbox">
        <h3>Using this screen</h3>
        <div class="inside">
            <p>Use this screen to add new articles or edit existing ones. Make sure you click 'Publish' to publish a new article once you've added it, or 'Update' to save any changes.</p>
        </div><!-- .inside -->
    </div><!-- .postbox -->
  <?php
  }
//add_action( 'edit_form_after_title', 'wptutsplus_text_after_title' );

  //saving custom meta boxes content
  function turbotabs_meta_save($post_id){

   if(defined('DOING_AUTOSAVE') && 'DOING_AUTOSAVE') {
      return $post_id;
    }
   global $post;
  // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

      if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
      }
    } else {
      if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
      }
    }

  $metadata['turbotab_icon']  = ( isset( $_POST['turbotab_icon'] ) ? $_POST['turbotab_icon'] : '' );
  $metadata['turbotab_sub']  = ( isset( $_POST['turbotab_sub'] ) ? sanitize_text_field( $_POST['turbotab_sub'] ) : '' );

  foreach( $metadata as $key => $value ){

    $current_value = get_post_meta($post_id, $key, true);
    if( $value && '' == $current_value ){
       add_post_meta( $post_id, $key, $value, true );
    } 
      elseif( $value && '' != $current_value ){
        update_post_meta( $post_id, $key, $value );
    } 
      elseif ( '' == $value &&  $current_value ){
       delete_post_meta( $post_id, $key, $current_value );
      }
   }
  }
  add_action( 'save_post', 'turbotabs_meta_save' );
  /*===========================================================================
                                Edit Term Page
                                 ============

        Inserting the main turbotabs option for the current group
        (the one being edited) along with the live preview window.                       

                                 inspired by:
        https://pippinsplugins.com/adding-custom-meta-fields-to-taxonomies/
  =============================================================================*/
  function turbotabs_group_meta_options($term) {
   
    // put the term ID into a variable
    $t_id = $term->term_id;
    
    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_option( "taxonomy_$t_id" );

    // extracting the tabs (turbotabs custom posts) for the preview section
    $arg = array(
          'post_type'   => 'turbotabs',
          'posts_per_page' => 3,
          'tax_query'   => array(
                array(
                  'taxonomy'  => 'turbotabs_group',  
                  'field'     => 'term_id',  
                  'terms'     => $t_id,  
                )      
           )
      );
     $tabs = get_posts($arg); 
     $index = 0;
     ?>
     <tr id="turbotabs-options" class="form-field">
      <th scope="row" valign="top"><label for="term_meta[custom_term_meta]"><?php _e( 'Customization Options', 'turbotabs' ); ?></label></th>
        <td>
         <div class="options par"> 
         <ul class="prev-navs navs">
                <li class="active"><i class="fa fa-film"></i> <?php _e('Basics', 'turbotabs'); ?></li>
                <li><i class="fa fa-paint-brush"></i> <?php _e('Colors','turbotabs'); ?></li>
                <li><i class="fa fa-leaf"></i> <?php _e(':Hover Colors','turbotabs'); ?></li>
                <li><i class="fa fa-expand"></i> <?php _e('Dimensions','turbotabs'); ?></li>
          </ul>

          <div class="prev-cont cont">
              <div class="prev pane active"> 
                <div class="group blc">
                     <label><?php _e('Set Active Tab','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                     <span class="question-bulb">Active tab is the one that will be opened by default when page loads.</span>
                     <select id="index" name="term_meta[index]">
                          <?php foreach( $tabs as $tab ): setup_postdata($tab); ?>
                          <option value="<?php echo $index; ?>" <?php if( $term_meta['index'] == $index ) echo 'selected="selected"'; ?>><?php echo apply_filters('the_title', $tab->post_title); ?></option>           
                          <?php $index++; endforeach; ?>
                     </select>
                  </div>  
                  <div class="group blc">
                     <label><?php _e('Select Layout', 'turbotabs'); ?></label>
                     <select id="layout" name="term_meta[layout]">
                        <option value="" <?php if( $term_meta['layout'] === 'turbotabs' ) echo 'selected="selected"' ; ?>>TurboTab</option>
                        <option disabled="disabled">Classic</option>
                        <option disabled="disabled">Hollow</option>
                        <option disabled="disabled">Simple</option>               
                     </select>
                     <span class="pro">Upgrade to PRO version to unlock all 4 Layouts</span>
                  </div>
                  <div class="group blc">
                      <label><?php _e('Select an animation','turbotabs'); ?></label> <span id="anACC">Not available for accordion mode</span>
                      <select id="effect" name="term_meta[animation]">
                          <option value="Scale" <?php if( $term_meta['animation'] === 'Scale' ) echo 'selected="selected"' ; ?>>Scale</option>
                          <option value="Bounce" <?php if( $term_meta['animation'] === 'Bounce' ) echo 'selected="selected"' ; ?>>Bounce</option>
                          <option value="FadeUp" <?php if( $term_meta['animation'] === 'FadeUp' ) echo 'selected="selected"' ; ?>>Fade-Up</option>
                          <option value="FadeLeft" <?php if( $term_meta['animation'] === 'FadeLeft' ) echo 'selected="selected"' ; ?>>Fade-Left</option>
                      </select>
                      <span class="pro">28 Animations Available in PRO version</span>
                  </div>        
                  <div class="group blc">
                     <label><?php _e('Select Mode','turbotabs'); ?></label>
                     <select id="mode" name="term_meta[mode]">
                        <option value="horizontal" <?php if( $term_meta['mode'] === 'horizontal' ) echo 'selected="selected"' ; ?>>Horizontal</option>
                        <option value="vertical" <?php if( $term_meta['mode'] === 'vertical' ) echo 'selected="selected"' ; ?>>Vertical</option>
                        <option value="accordion" <?php if( $term_meta['mode'] === 'accordion' ) echo 'selected="selected"' ; ?>>Accordion</option>               
                     </select>
                  </div>
                  <div class="group">
                      <label><?php _e('Navigation Tabs Position','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">Navigation Tabs Position will affect the <em>position</em> of the navigation tabs. You can choose to move them to Top or to Bottom for <em>horizontal tab</em>, and between Left or Right for <em>vertical tab</em>. This is not available for <em>accordion</em> mode.</span>
                      <span class="pro">Available in PRO version</span>
                  </div>
                  <div class="group">
                      <label><?php _e('Navigation Tabs Align','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">This will change the <em>alignment</em> of the navigation tabs. You can choose between Left, center or Right align for the <em>horizontal tab</em>, or Top, Middle or Bottom align for <em>vertical tab</em>. This is not available for <em>accordion</em> mode.</span>
                      <span id="alACC">Not available for accordion mode</span>
                      <select id="alignH" class="alCH" name="term_meta[align_h]">
                          <option value="left" <?php if( $term_meta['align_h'] === 'left' ) echo 'selected="selected"' ; ?>>Left</option>
                          <option value="right" <?php if( $term_meta['align_h'] === 'right' ) echo 'selected="selected"' ; ?>>Right</option>
                          <option value="center" <?php if( $term_meta['align_h'] === 'center' ) echo 'selected="selected"' ; ?>>Center</option>
                      </select>    
                      <select id="alignV" class="alCH" name="term_meta[align_v]">
                          <option value="top" <?php if( $term_meta['align_v'] === 'top' ) echo 'selected="selected"' ; ?>>Top</option>
                          <option value="middle" <?php if( $term_meta['align_v'] === 'middle' ) echo 'selected="selected"' ; ?>>Middle</option>
                          <option value="bottom" <?php if( $term_meta['align_v'] === 'bottom' ) echo 'selected="selected"' ; ?>>Bottom</option>
                      </select> 
                  </div>
                  <div class="group">
                     <label><?php _e('Float Tab', 'turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                     <span class="question-bulb">Floating means to put your tab in same row with some other content. To <em>float</em> it to the left means that tab will be pushed to the left side with some other content. To <em>float</em> it to the right is opposite. For best results it is desirable for you to style that other element to be floated to the opposite side of the side you set the tab to float to. Eg. if tab is set to float: left, your other element should have float: right style. If you want to display this tab in same row( or line ) with some text (paragraph) or image, then floating it to the left, or to the right will not cause any undesirable effect.</span>
                     <span class="pro">Available in PRO version</span>
                  </div>
                  <div class="group">
                      <label><?php _e('Turn On/Off Nav and heading title text shadow:','turbotabs'); ?></label>
                      <select id="shadow" name="term_meta[shadow]" >
                          <option value="no" <?php echo $term_meta['shadow'] === 'no' ? 'selected="selected"' : ''; ?>>No</option>
                          <option value="yes" <?php echo $term_meta['shadow'] === 'yes' ? 'selected="selected"' : ''; ?>>Yes</option>
                      </select>
                      <div class="group shd">
                          <label><?php _e('Choose between dark/light', 'turbotabs'); ?></label>
                          <select id="shd" name="term_meta[shadow_shade]">
                              <option value="dark" <?php if( $term_meta['shadow_shade'] === 'dark' ) echo 'selected="selected"' ; ?>>Dark</option>
                              <option value="light" <?php if( $term_meta['shadow_shade'] === 'light' ) echo 'selected="selected"' ; ?>>Light</option>
                          </select>
                      </div >
                  </div>
                   <div class="group blc">
                     <label>Force Tab To Take <em>(resize to)</em> It's Content Height <i class="qst fa fa-question"></i></label>
                     <span class="question-bulb">By default tab content's height will be <em>fixed</em> <i>(taking the height of the tallest tab)</i> as you switch trought tabs. If you enable (select yes) this option, then tab content box will <em>resize</em> to fit currently <i>(active, opened one)</i> tab.</span>
                     <select id="force_height" name="term_meta[force_height]">
                        <option value="no" <?php if( $term_meta['force_height'] === 'no' ) echo 'selected="selected"' ; ?>>No</option>
                        <option value="yes" <?php if( $term_meta['force_height'] === 'yes' ) echo 'selected="selected"' ; ?>>Yes</option>             
                     </select>
                  </div>
              </div>    
              <div class="prev pane">
                  <div class="group">
                      <label for="icon-c"><?php _e('Icon Color','turbotabs'); ?> </label>
                      <input type="text" class="color" id="icon-c" name="term_meta[icon_color]" value="<?php echo esc_attr($term_meta['icon_color']) ? esc_attr($term_meta['icon_color']) : '#fff'; ?>"/>
                  </div>
                  <div class="group">
                      <label><?php _e('Title Color','turbotabs'); ?></label>
                      <input type="text" class="color" id="ttl" name="term_meta[title_color]" value="<?php echo esc_attr($term_meta['title_color']) ? esc_attr($term_meta['title_color']) : 'palegoldenrod'; ?>" />
                  </div>
                  <div class="group">
                      <label><?php _e('SubTitle Color','turbotabs'); ?></label>
                      <span class="pro">Available in PRO version</span>
                  </div>
                  <div class="group">
                      <label><?php _e('Content Text Color','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">Text Color affects the color of the tab content. <br/><em class="note">NOTE</em>: Some themes can override this option, I didn't "forced" this option to be absolute, so if content color appears differently than your selection it means your theme has custom style that affects all paragraphs in the post/page. In that case you can always change text color in your post/page tinyMCE editor by selecting your content and then clicking the <em>Text Color</em> button.</span>
                      <input type="text" class="color" id="txt" name="term_meta[text_color]" value="<?php echo esc_attr($term_meta['text_color']) ? esc_attr($term_meta['text_color']) : '#FFFFFF'; ?>" />
                  </div>
                  <div class="group">
                      <label><?php _e('Tab Navigation Background Color','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">This applies to the background of the tab navigation in its default state (not <em>:hovered</em> or <em>active</em> (selected) ). <br/><em class="note">NOTE:</em> this applies to the <em>Classic</em> layout when in <em>Accordion</em> mode. It will fill the space between heading and it's surrounding borders.</span>
                      <span class="simpleNO smpAcc">Not available for layout - <em>Simple</em></span>
                      <span class="hollowNO">Not available for layout - <em>Hollow</em></span>
                      <input type="text" class="color" id="tabNC" name="term_meta[navigation_color]" value="<?php echo esc_attr($term_meta['navigation_color']) ? esc_attr($term_meta['navigation_color']) : '#929292'; ?>" />
                  </div>
                  <div class="group">
                      <label><?php _e('Tab Content Background Color','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">This applies to, as it's title stats, to the tab content background. <br/><em class="note">NOTE</em>: For <em>Simple</em> and <em>Classic</em> layouts this option applies to on <em>:hover</em> state and <em>active</em> tab navigation color as well, because they are designed to have the same color as tab <em>content background</em>.</span>
                      <span class="hollowNO">Not available for layout - <em>Hollow</em></span>
                      <input type="text" class="color" id="tabCB" name="term_meta[content_color]" value="<?php echo esc_attr($term_meta['content_color']) ? esc_attr($term_meta['content_color']) : '#444A53'; ?>" />
                  </div>
                  <div class="group">
                      <label><?php _e('Border Color','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">This will apply to the color of the tab content border. This option will not work with <em>TurboTab</em> Layout.</span>
                      <span class="pro">Available in PRO version</span>
                  </div>
                  <div class="group">
                      <label><?php _e('Accordion heading Background Color','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">This applies to the background of the heading when in <em>accordion</em> mode ( selected by default, or transformed to it, on smaller screens and devices ). <br/><em class="note">NOTE</em>This does not work with <em>Simple</em> layout. The heading will inherit a color you set as the tab content background.</span>
                      <span class="hollowNO">Not available for layout - <em>Hollow</em></span>
                      <span class="simpleNO smpAcc">Not available for layout - <em>Simple</em>. Heading color will be inherited from <b>Tab Content Background Color</b> option</span>
                      <span class="taNO">For <em>TurboTab</em> layout in accordion mode use <em>Tab navigation background color as heading background color</em></span>
                      <input type="text" class="color" id="hedb" name="term_meta[heading_bck]" value="<?php echo esc_attr($term_meta['heading_bck']) ? esc_attr($term_meta['heading_bck']) : '#FFFFFF'; ?>" />
                  </div>
              </div>
              <div class="prev pane">    
                  <div class="group">
                      <label><?php _e('Tab Navigation on :Hover Text Color','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">This affects tab navigation color when it is hovered ( mouse enters the tab navigation area). It is applied to all modes. When tab is transformed to <em>accordion</em>, or it is it's default mode - then this option will affect color of the <em>accordion heading</em>.</span>
                      <span class="pro">Available in PRO version</span>
                  </div>
                  <div class="group">
                      <label><?php _e('Tab Navigation on :Hover Background Color','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">This affects tab navigation background color when it is hovered ( mouse enters the tab navigation area). It is applied to all modes. When tab is transformed to <em>accordion</em>, or it is it's default mode - then this option will affect background color of the <em>accordion heading</em>. <br/><em class="note">NOTE:</em> This option is not available for <em>Simple</em> layout, except when in accordion mode</span>
                      <span class="hollowNO">Not available for layout - <b>Hollow</b></span>
                      <span class="simpleNO">Not available for layout - <b>Simple</b> - except when in <b>Accordion Mode</b></span>
                      <span class="classicNO">Not available for layout - <b>Classic</b></span>
                      <input type="text" class="color" id="hoverB" name="term_meta[hover_bck]" value="<?php echo esc_attr($term_meta['hover_bck']) ? esc_attr($term_meta['hover_bck']) : '#444A53'; ?>" />
                  </div>
                  <div class="group">
                      <label><?php _e('Tab Navigation Selected (active one) Background Color','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">This affects tab navigation background color of the selected/active tab. Active tab is the one that is currently viewed. Once it is clicked, it becomes the <em>"active"</em> one and hence, this option is applied. Considering active tab color, the same color from - on <em>:hover text color</em> - will be applied.</span>
                      <span class="hollowNO">Not available for layout - <b>Hollow</b></span>
                      <span class="simpleNO">Not available for layout - <b>Simple</b> - except when in <b>Accordion Mode</b></span>
                      <span class="classicNO">Not available for layout - <b>Classic</b></span>
                      <input type="text" class="color" id="activeB" name="term_meta[active_bck]" value="<?php echo esc_attr($term_meta['active_bck']) ? esc_attr($term_meta['active_bck']) : '#444A53'; ?>" />
                  </div>
                  <div class="bordAC group">
                      <label><?php _e('Selected (active one) Tab Navigation Border Color','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">This will apply to the <em>active</em> (selected) heading border color of the <em>accordion mode</em> and to the <em>Hollow</em> layout where it will create an underline effect for the selected tab. This option will not work with <em>TurboTab</em> Layout in <em>accordion</em> mode.</span>
                      <span class="pro">Available in PRO version</span>
                  </div>
              </div>    
              <div class="prev pane">
                  <div class="group not">
                      <label><?php _e('Tab Width (in %)','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb"><em>Width</em> determines the horizontal length of your tab. <em>Padding</em> is also calculated in final <em>width</em>. Padding (don't forget to double it - more info in the help bulb next to the <em>Padding</em> field) + Width must be equal to 100% (if you are using percents and want your tab to has responsive flow)<br><em class="note">NOTE</em> Width in px will override value in %. To use percents make sure that value in px is set to 0 or empty.</span>
                      <input type="range" id="width-s" min="10" max="100" name="term_meta[width_s]" value="<?php echo esc_attr($term_meta['width_s']) ? esc_attr($term_meta['width_s']) : '75'; ?>" /><label id="width"></label>
                      <p>If you want to set width in pixels enter your value here: <input id="width-p" type="number" step="20" name="term_meta[width_p]" value="<?php echo esc_attr($term_meta['width_p']) ? esc_attr($term_meta['width_p']) : ''; ?>" /></p>
                  </div>
                  <div class="group not">
                      <label><?php _e('Padding (in %)','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">Padding will affect the position of your tab. The unit selected is applied to both sides (eg. if you select 20% it will <b>apply</b> as <u>padding-left: 20% and padding-right: 20%</u>). This applies to both px an %. This is useful for centering the tab on the screen. To center it you need to calculate <em>width</em> and <em>padding</em>. Padding (don't forget to double it) + Width must be equal to 100% (if you are using percents and want your tab to has responsive flow). <br><em class="note">NOTE</em> Padding in px will override value in %. To use percents make sure that value in px is set to 0 or empty.</span>
                      <input type="range" id="padd-s" min="0" max="40"  name="term_meta[padd_s]" value="<?php echo esc_attr($term_meta['padd_s']) ? esc_attr($term_meta['padd_s']) : '0'; ?>" /><label id="padd"></label>
                      <p>If you want to set padding in pixels enter your value here: <input id="padd-p" type="number" step="5" name="term_meta[padd_p]" value="<?php echo esc_attr($term_meta['padd_p']) ? esc_attr($term_meta['padd_p']) : ''; ?>" /></p>
                  </div>
                  <div class="group not">
                      <label><?php _e('Border Radius','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">This applies to the corners of the tab container ( part that holds tab content ). To increase the <em>radius</em> of the tab container corners slide the range handle to the right. To decrease it, slide to the left</span>
                      <span class="pro">Available in PRO version</span>
                  </div>
                  <div class="group not">
                      <label><?php _e('Box Shadow','turbotabs'); ?> <i class="qst fa fa-question"></i></label>
                      <span class="question-bulb">This will affect the <em>spread of the shadow</em>. To increase the <em>spread</em> slide the range hadle to the right, to decrease it slide to the left.</span>
                      <span class="pro">Available in PRO version</span>
                  </div>
              </div>
              <div class="tt-assist">
                <p>
                   If you want unlock all the features <a href="http://themeflection.com/plug/responsive-wordpress-tabs/" target="_BLANK"> Upgrade to PRO</a>.
                   If you need assistence reffer to the <a href="<?php echo site_url() . '/wp-admin/edit.php?post_type=turbotabs&page=tt-help' ?>"><b>help</b></a> file. If you still can't find the answer you are looking for, visit my <a href="http://themeflection.com/support/">support forum</a> or contact me.
                   <span class="ast-hide"><i class="fa fa-times">Hide</i></span>
                </p>
                <div class="tt_head"></div>
             </div>
          </div>  
          </div><!-- .options -->
          <footer id="upgrade">
              <p><a href="http://themeflection.com/plug/responsive-wordpress-tabs/" target="_BLANK">Upgrade</a> to PRO Version and unlock all the features.</p>
          </footer>
          </td>
          </tr>
          <div id="live_preview">
                <a class="preview-open">
                  <i class="fa fa-chevron-down"></i>
                  <span>Live Preview</span>
                </a>
                <?php if( $tabs ): ?>
                <div class="main par">
                    <!-- - - - - - Tab navigation - - - - - - -->
                    <ul class="tabs navs">
                    <?php foreach(  $tabs as $tab ): setup_postdata($tab); 
                       $subtitle = get_post_meta( $tab->ID, 'turbotab_sub', true ); ?>
                       <li><?php echo '<span class="nav-hld"><i class="fa ' . get_post_meta($tab->ID, 'turbotab_icon', true) . '"></i>' . '<span class="ttnav-title">' . apply_filters('the_title', $tab->post_title) . '</span></span><span class="ttnav-sbt">'. $subtitle .'</span>'; ?></li> 
                    <?php endforeach; ?>
                    </ul>
                    <!-- - - - - Tab Content - - - - - -->
                    <div class="container cont">
                        <?php foreach(  $tabs as $tab ): setup_postdata($tab); ?>
                        <div class="tab">
                           <p><?php echo apply_filters( 'the_content', $tab->post_content ); ?></p>     
                        </div>
                        <?php endforeach; wp_reset_postdata(); ?>
                    </div><!-- .container -->
                </div>
                <?php else: ?>
                <div class="no-ttabs">
                   <h3>Empty</h3>
                   <h4>There are no tabs to display at the moment</h4>
                   <p>This means that you have not assigned any tabs to this <em>Group</em>. To do so go to <em>TurboTabs</em> and then select <em>All Tabs</em>.</p>
                   <p>From here you can edit already created tabs by assigning them to this <em>Group</em>. If you havent added any tabs yet, go to <em>TurboTabs</em> and then <em>Add Tab</em>.</p>
                   <div class="tt_image"><span class="tt_image_inner"><span class="img-smg"><i class="fa fa-cog fa-spin fa-4x"></i></span></span></div>
                </div>  
               <?php endif; ?>
          </div>       
  <?php  
  }
  add_action( 'turbotabs_group_edit_form_fields', 'turbotabs_group_meta_options', 10, 2 );

  /*=================================================
     Save extra taxonomy fields callback function.
  ==================================================*/   
  function save_taxonomy_custom_meta( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
      $t_id = $term_id;
      $term_meta = get_option( "taxonomy_$t_id" );
      $cat_keys = array_keys( $_POST['term_meta'] );
      foreach ( $cat_keys as $key ) {
        if ( isset ( $_POST['term_meta'][$key] ) ) {
          $term_meta[$key] = $_POST['term_meta'][$key];
        }
      }
      // Save the option array.
      update_option( "taxonomy_$t_id", $term_meta );
    }
  }  
  add_action( 'edited_turbotabs_group', 'save_taxonomy_custom_meta', 10, 2 );
  /*=================================================
           if on $turbotabs post type screen 
     add help tab to the top of the screen
  ==================================================*/
  function add_current_tabs_preview(){
   global $current_screen, $post;
   $type = $current_screen->post_type; 
   $tax = $current_screen->taxonomy;
   $id= $current_screen->id;
   if ( $type === 'turbotabs' && $tax != 'turbotabs_group' && $id != 'edit-turbotabs' && $id != 'turbotabs_page_tt-help' ) { // if on the post screen, not taxonomy
     $id = $post->ID;
     $term = wp_get_post_terms($id, 'turbotabs_group');
     $t_id = ''; 
     if( $term ) $t_id = $term[0]->term_id;
     ?>
     <div id="live_preview">
            <a class="preview-open">
              <i class="fa fa-folder-o"></i>
              <span>TurboTabs</span>
            </a>
               <div class="no-ttabs">
                  <h3><i class="fa fa-desktop"></i> Quick Guide</h3>
                    <h4>This is a quick intro to TurboTabs Plugin</h4>
                    <p> Currently you are building a tab that can be assigned to some group. Until you do not do that, you won't have fully functional tab to display.</p>
                    <?php if($term): ?><p>To edit the appearance and other options for this tabs group you need to go to the <em>Groups</em> (under the TurboTab dashboard menu) and edit your <em><b><?php echo $term[0]->name; ?></b></em> Group.</p><?php endif; ?>
                    <p>You are using the TurboTabs LIGHT Version. In free version there is a limit of <em>3 tabs</em> per group. To remove this limit and unlock all features <a style="color: indianred;" href="http://themeflection.com/plug/responsive-wordpress-tabs/" target="_BLANK"> Upgrade</a> to PRO Version.</p>
                  <h3><span class="fa fa-wrench"></span> Settings</h3>
                   <?php if(!$term): ?>
                    <p>If this is your first tab, you need to create new group to which it will be assigned to, together with other tabs (if you plan to have more than one tab).</p>
                    <p>To create one select from the <i>dashboard admin menu</i> <em>Tabs->Groups->Add Group</em></p>
                  <?php endif; ?>
                  <p>If you are unsure what to do next, you have few steps available:</p>
                  <ul>
                    <li>Create new group if not already</li>
                    <li>Add more tabs to the group</li>
                    <li>Customize <?php if($term): echo $term[0]->name; ?> Group <?php else: ?> some of the available groups <?php endif; ?></li>
                    <li>Check plugin documentation</li>
                  </ul>
                  <p>If you are not sure how to edit your new group reffer to documentation file that can be found under your dashboard menu. Go to <em>TurboTabs->Help</em> or simply click this <a href="<?php echo site_url() . '/wp-admin/edit.php?post_type=turbotabs&page=tt-help' ?>"><em>link</em></a> <sup><i class="fa fa-link"></i></sup>.</p>
                   <div class="tt_image"><span class="tt_image_inner"><span class="img-smg"><i class="fa fa-cog fa-spin fa-4x"></i></span></span></div>
               </div>
     </div>
    <?php
    } else {
      return;
    }
  } //add_current_tabs_preview
  add_action('admin_head', 'add_current_tabs_preview');
  /*====================================
        Help Page
  =====================================*/      
  function turbotabs_help() {
    if( is_admin() ){
      // Add the top level menu page
      add_submenu_page( 'edit.php?post_type=turbotabs', "TurboTabs Help", "Help", 'manage_options', "tt-help", "turbotabs_help_file");
    }
  }
  add_action('admin_menu', 'turbotabs_help');
  // callback function
  function turbotabs_help_file() {
    ?>
    <div>
    <?php echo screen_icon(); ?>
    <h2>TurboTabs Help</h2>
      <iframe id="help_id" frameborder="0" src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/help/intro.html'; ?>"  border="0"></iframe>  
    </div>
   <?php 
  }  
?>