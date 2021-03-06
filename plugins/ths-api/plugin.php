<?php
/*
 * Plugin Name: THS API
 * Plugin URI:
 * Description: JSON endpoint and shortcode for THS Website
 * Version: 1.1
 * Author: statecs
 * Author URI: http://statecreative.se
 * License: MIT
 */

/*
 *
 *  FILE STRUCTURE:
 *
 *  1. WP-API
 *  2. CUSTOM ENDPOINTS
 *  3. ADD \ REGISTER MENUS
 *  4. ADD PAGE TEMPLATES
 *  5. ADD \ REMOVE ACTIONS
 *  6. ADD REQUIRED PLUGINS
*/

/**
* Shortcode
*/

if( function_exists('acf_add_options_page') ) {
  
  acf_add_options_page(array(
    'page_title'  => 'Card General Settings',
    'menu_title'  => 'Cards',
    'menu_slug'   => 'card-general-settings',
    'capability'  => 'publish_posts',
    'redirect'    => false
  ));

    acf_add_options_sub_page(array(
    'page_title'  => 'Membership Card',
    'menu_title'  => 'Membership Card',
    'capability'  => 'publish_posts',
    'parent_slug' => 'card-general-settings'
  ));

  acf_add_options_sub_page(array(
    'page_title'  => 'Events Card',
    'menu_title'  => 'Events Card',
    'capability'  => 'publish_posts',
    'parent_slug' => 'card-general-settings'
  ));

  acf_add_options_sub_page(array(
    'page_title'  => 'Nymble Menu Card',
    'menu_title'  => 'Nymble Menu Card',
    'capability'  => 'publish_posts',
    'parent_slug' => 'card-general-settings'
  ));


   acf_add_options_sub_page(array(
    'page_title'  => 'Restrictions Card',
    'menu_title'  => 'Restrictions Card',
    'capability'  => 'publish_posts',
    'parent_slug' => 'card-general-settings'
  ));

  acf_add_options_sub_page(array(
    'page_title'  => 'Gallery Card',
    'menu_title'  => 'Gallery Card',
    'capability'  => 'publish_posts',
    'parent_slug' => 'card-general-settings'
  ));

  acf_add_options_sub_page(array(
    'page_title'  => 'Kårbokhandel Card',
    'menu_title'  => 'Kårbokhandel Card',
    'capability'  => 'publish_posts',
    'parent_slug' => 'card-general-settings'
  ));

    acf_add_options_sub_page(array(
    'page_title'  => 'Campus Competence Card',
    'menu_title'  => 'Campus Competence Card',
    'capability'  => 'publish_posts',
    'parent_slug' => 'card-general-settings'
  ));

  acf_add_options_sub_page(array(
    'page_title'  => 'FAQ Card',
    'menu_title'  => 'FAQ Card',
    'capability'  => 'publish_posts',
    'parent_slug' => 'card-general-settings'
  ));

    acf_add_options_sub_page(array(
    'page_title'  => 'KarX Card',
    'menu_title'  => 'Karx Card',
    'capability'  => 'publish_posts',
    'parent_slug' => 'card-general-settings'
  ));
  
}

function foo_register_alt_version_features($features) {
  $features['custom-fields'] = array();
  return $features;
}

add_filter('bu_alt_versions_feature_support', 'foo_register_alt_version_features');

// 2. Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_unique_id( $version ) {
  include_once(__DIR__.'/inc/acf-unique_id-v5.php');
}

add_action('acf/include_field_types', 'include_field_types_unique_id');


/* include static class */
include_once( __DIR__.'/inc/class-ths-postypes.php' );
/* add_shortcode('dummy', array( 'THS_Shortcode', 'seances') );
add_filter('get_the_excerpt', 'do_shortcode', 99);*/

add_filter( 'rest_url_prefix', function( $prefix ) { return 'api'; } );

/**
* 1. WP API
*/

/* include static class */
include_once( __DIR__.'/inc/class-ths-api.php' );


/**
* 2. CUSTOM ENDPOINTS
*/
add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/sticky/(?P<id>\d+)', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array('THS_API', 'get_sticky_posts'),
    ));
});


add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/post/(?P<url>\S+)', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array('THS_API', 'get_posts_from_url'),
        'args'     => array(
			'url' => array( 'required' => true )
		),
    ));
});

add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/social/(?P<type>\S+)', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array('THS_API', 'get_social_posts'),
        'args'     => array(
            'type' => array( 'required' => true )
        ),
    ));
});

 /* ------------
  3. ADD \ REGISTER MENUS
 --------------- */
    register_nav_menus( array(
        'header_menu' => 'Main Menu',
        'footer_menu' => 'Footer Menu',
        'chapters_menu' => 'Chapters Menu',
    ) );


/* ------------
    4. ADD PAGE TEMPLATES
    Custom Page Templates can be added quickly in the array under $templates.
    When adding a custom template, angular will look for an html file in the directory
    ./partials/pages/
    For example if you add:
    'template-example' => 'Template Example"
    You will need to place your template html in:
    ./partials/pages/template-example.html
    In that file you can call all of the page information using {{post}}
    For Example: {{post.title.rendered}} will display the title
    --------------- */
    add_filter( 'custom_page_templates', function( $now_templates ) {
        $templates = array(
            'home-page'  => 'Home Page',
            'faq'  => 'FAQ',
            'nymble-restaurant' => 'Nymble Restaurant'
        );
        return array_merge( $now_templates, $templates );
    } );
    function get_custom_page_templates() {
        $templates = array();
        return apply_filters( 'custom_page_templates', $templates );
    }
    function custom_page_templates_init() {
        remove_action( current_filter(), __FUNCTION__ );
        if ( is_admin() && get_current_screen()->post_type === 'page' ) {
            $templates = get_custom_page_templates(); // the function above
            if ( ! empty( $templates ) )  {
                set_custom_page_templates( $templates );
            }
        }
    }
    function custom_page_templates_init_post() {
        remove_action( current_filter(), __FUNCTION__ );
        $method = filter_input( INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING );
        if ( empty( $method ) || strtoupper( $method ) !== 'POST' ) return;
        if ( get_current_screen()->post_type === 'page' ) {
            custom_page_templates_init();
        }
    }
    function set_custom_page_templates( $templates = array() ) {
        if ( ! is_array( $templates ) || empty( $templates ) ) return;
        $core = array_flip( (array) get_page_templates() ); // templates defined by file
        $data = array_filter( array_merge( $core, $templates ) );
        ksort( $data );
        $stylesheet = get_stylesheet();
        $hash = md5( get_theme_root( $stylesheet ) . '/' . $stylesheet );
        $persistently = apply_filters( 'wp_cache_themes_persistently', false, 'WP_Theme' );
        $exp = is_int( $persistently ) ? $persistently : 1800;
        wp_cache_set( 'page_templates-' . $hash, $data, 'themes', $exp );
    }


function my_rest_prepare_post( $data, $post, $request ) {
	$_data = $data->data;
	$thumbnail_id = get_post_thumbnail_id( $post->ID );
	$thumbnail = wp_get_attachment_image_src( $thumbnail_id, 'full' );
	$_data['featured_image_thumbnail_url'] = $thumbnail[0];
	$data->data = $_data;
	return $data;
}
add_filter( 'rest_prepare_post', 'my_rest_prepare_post', 10, 3 );


/* Remove X-Pingback in the HTTP header */
add_filter('wp_headers', function($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});

/* Remove link to Rest API in the HTTP header */
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );


/* ------------
    5. ADD \ REMOVE ACTIONS
--------------- */
    add_action( 'edit_form_after_editor', 'custom_page_templates_init' );
    add_action( 'load-post.php', 'custom_page_templates_init_post' );
    add_action( 'load-post-new.php', 'custom_page_templates_init_post' );

    add_action('add_meta_boxes','wp_add_post_custom_template');
    add_action('save_post','wp_save_custom_post_template',10,2);
    add_filter('single_template','wp_get_custom_post_template_for_template_loader');

function wp_add_post_custom_template($postType) {
    
    if(get_option('wp_custom_post_template') == ''){ //get option value
        $postType_title = 'post';
        $postType_arr[] = $postType_title;
    }else{
        $postType_title = get_option('wp_custom_post_template');
        $postType_arr = explode(',',$postType_title);
    }
    if(in_array($postType, $postType_arr)){
        add_meta_box(
                'postparentdiv',
                __('WP Post Template'),
                'wp_custom_post_template_meta_box',
                $postType,
                'side', 
                'core'
        );
    }
}
function wp_custom_post_template_meta_box($post) {
    if ( $post->post_type != 'page' && 0 != count( wp_get_post_custom_templates() ) ) {
        $template = get_post_meta($post->ID,'_post_template',true);
    ?>
        <label class="screen-reader-text" for="post_template"><?php _e('Post Template') ?></label>
        <select name="post_template" id="post_template">
            <option value='news'><?php _e('Default Template'); ?></option>
            <?php wp_custom_post_template_dropdown($template); ?>
        </select>
        <p><i><?php _e( 'Some themes have custom templates you can use for single posts template selecting from dropdown.'); ?></i></p>
    <?php
    }
}?>
<?php 
function wp_get_post_custom_templates() {
  if(function_exists('wp_get_themes')){
        $themes = wp_get_themes();
    }else{
        $themes = get_themes();
    }           
    
  $theme = wp_get_theme();
  $templates = array(
            'news'  => 'News',
        );

  return $templates;
}

function wp_custom_post_template_dropdown( $default = '' ) {
  $templates = array_flip( (array) wp_get_post_custom_templates() );
  ksort( $templates );
  foreach (array_keys( $templates ) as $template )
    : if ( $default == $templates[$template] )
      $selected = " selected='selected'";
    else
      $selected = '';
  echo "\n\t<option value='".$templates[$template]."' $selected>$template</option>";
  endforeach;
}

function wp_save_custom_post_template($post_id,$post) {
  if ($post->post_type !='page' && !empty($_POST['post_template']))
    update_post_meta($post->ID,'_post_template',$_POST['post_template']);
}

function wp_get_custom_post_template_for_template_loader($template) {
  global $wp_query;
  $post = $wp_query->get_queried_object();
  if ($post) {
    $post_template = get_post_meta($post->ID,'_post_template',true);

  //  if (!empty($post_template) && $post_template!='default')
    //  $template = get_stylesheet_directory() . "/{$post_template}";
  }
  
  return $template;
}

// Add custom post template rule to dropdown
add_filter('acf/location/rule_types', 'acf_location_rules_types');
function acf_location_rules_types( $choices ){
  $choices['Post']['cpt'] = 'Post Template';
  $choices['Page']['cpa'] = 'Page Template';
  return $choices;
}

// Add custom post template names to value dropdown
add_filter('acf/location/rule_values/cpt', 'acf_location_rules_values_cpt');
function acf_location_rules_values_cpt( $choices ){
  $templates = wp_get_post_custom_templates();
    foreach($templates as $k => $v){
      $choices[$k] = $v;
    }
  return $choices;
}
// Add custom post template names to value dropdown
add_filter('acf/location/rule_values/cpa', 'acf_location_rules_values_cpa');
function acf_location_rules_values_cpa( $choices ){
  $templates = get_custom_page_templates();
    foreach($templates as $k => $v){
      $choices[$k] = $v;
    }
  return $choices;
}

// Match location rule and show ACFs
add_filter('acf/location/rule_match/cpt', 'acf_location_rules_match_cpt', 10, 3);
function acf_location_rules_match_cpt( $match, $rule, $options ){
  global $post;
  if(isset($options['cpt'])){
    $current_post_template = $options['cpt'];   
  }else{
    $current_post_template = get_post_meta($post->ID,'_post_template',true);
  }
  $selected_post_template = $rule['value'];
  if($rule['operator'] == "=="){
    $match = ( $current_post_template == $selected_post_template );
  }elseif($rule['operator'] == "!="){
    $match = ( $current_post_template != $selected_post_template );
  }
  return $match;
}

// Match location rule and show ACFs
add_filter('acf/location/rule_match/cpa', 'acf_location_rules_match_cpa', 10, 3);
function acf_location_rules_match_cpa( $match, $rule, $options ){
  global $post;
  if(isset($options['cpa'])){
    $current_post_template = $options['cpa'];   
  }else{
    $current_post_template = get_post_meta($post->ID,'_wp_page_template',true);
  }
  $selected_post_template = $rule['value'];
  if($rule['operator'] == "=="){
    $match = ( $current_post_template == $selected_post_template );
  }elseif($rule['operator'] == "!="){
    $match = ( $current_post_template != $selected_post_template );
  }
  return $match;
}

/**
 * Add automatic image sizes
 */
if ( function_exists( 'add_image_size' ) ) { 
      add_image_size( 'image640', 640, 640, true ); //(cropped)
      add_image_size( 'image960', 960, 400, false ); //(scaled)
      add_image_size( 'image1280', 1280, 400, false ); //(scaled)
      add_image_size( 'image1600', 1600, 550, false ); //(scaled)
       add_image_size( 'image1920', 1920, 550, false ); //(scaled)
}


function half_shortcode( $atts, $content = null ) {
  $a = shortcode_atts( array(
    'class' => 'o-half left-o',
  ), $atts );

  return '<div class="' . esc_attr($a['class']) . '">' . do_shortcode($content) . '</div>';
}
add_shortcode('o-half', 'half_shortcode');

function cover_shortcode( $atts, $content = null ) {

  return '<div class="cover-img" style="background-image:url(' . do_shortcode($content) . ');background-size: cover;"></div>';
}
add_shortcode('cover-img', 'cover_shortcode');

function collapse_shortcode( $atts, $content = null ) {
 
    $output = '';
 
    $pull_quote_atts = shortcode_atts( array(
        'quote' => 'My Quote',
    ), $atts );
 
   $output .= '<section><article> <input class="toggle-box" type="checkbox" id="' . $pull_quote_atts[ 'quote' ]  . '">';
    $output .=  '<div class="bg-c"><label class="bg-c1" for="' . $pull_quote_atts[ 'quote' ]  . '">' . $pull_quote_atts[ 'quote' ]  . '</label><div class="content-c">';
    $output .= '' . do_shortcode($content) . '';
    $output .= '</div></div></article></section>';
    return $output;
 
}
add_shortcode( 'collapse-quote', 'collapse_shortcode' );

function sub_collapse_shortcode( $atts, $content = null ) {
 
    $output = '';
 
    $pull_quote_atts = shortcode_atts( array(
        'quote' => 'My Quote',
    ), $atts );
 
    $output .= '<article><input class="toggle-box-2" type="checkbox" id="' . $pull_quote_atts[ 'quote' ]  . '">';
    $output .=  '<div class="bg-c"><label class="bg-c2" for="' . $pull_quote_atts[ 'quote' ]  . '">' . $pull_quote_atts[ 'quote' ]  . '</label><div class="content-c">';
    $output .= '' . do_shortcode($content) . '';
    $output .= '</div></div></article>';
 
    return $output;
 
}
add_shortcode( 'sub-collapse-quote', 'sub_collapse_shortcode' );


function my_searchwp_weight_mods( $sql ) {
  
  global $wpdb;
  
  // if posted within the last year, bump up the weight by 1000
  $time_ago = 'NOW() - INTERVAL 1 YEAR';
  $additional_weight = 1000;
  $sql .= " + ( IF( UNIX_TIMESTAMP( {$wpdb->prefix}posts.post_date ) > UNIX_TIMESTAMP( {$time_ago} ), {$additional_weight}, 0 ) )";
  return $sql;
  
}
add_filter( 'searchwp_weight_mods', 'my_searchwp_weight_mods' );


add_action('acf/save_post', 'clear_cache_on_options_save');
function clear_cache_on_options_save($post_id) {
  // you can check for options or not
  if ($post_id == 'options') {
    //wp_cache_clear_cache();
    wp_cache_post_change( 'options' );
  }
}

/* ------------
    6. ADD REQUIRED PLUGINS
--------------- */
    function required_plugins() {
        $plugin_error_message = array();
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        // Require the WP Rest API V2 Plugin
        if(!is_plugin_active( 'rest-api/plugin.php' )) {
            $plugin_error_message[] = 'This theme requires you to install the WP Rest API V2 plugin, <a href="https://wordpress.org/plugins/rest-api/">download it from here</a>.';
        }
        // Require the WP API Menus Plugin
        if(!is_plugin_active( 'wp-api-menus/wp-api-menus.php' )) {
            $plugin_error_message[] = 'This theme requires you to install the WP API Menus plugin, <a href="http://wordpress.org/plugins/wp-api-menus/">download it from here</a>.';
        }
        if(count($plugin_error_message) > 0) {
            echo '<div id="message" class="error">';
            foreach($plugin_error_message as $message) {
                echo '<p><strong>'.$message.'</strong></p>';
            }
            echo '</div>';
        }
    }


    add_filter( 'auto_core_update_send_email', 'wpb_stop_auto_update_emails', 10, 4 );
 
function wpb_stop_update_emails( $send, $type, $core_update, $result ) {
  
  if ( ! empty( $type ) && $type == 'success' ) {
    return false;
  }
    return true;
  }