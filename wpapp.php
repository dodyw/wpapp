<?php
/*
Plugin Name: WPAPP
Plugin URI: http://github.com/dodyrw/wpapp
Description: Secure data provider for WpApp WordPress mobile app.
Version: 1.0.0
Author: Dody Rachmat W.
Author URI: http://www.dodyrw.com/
*/

$dir = wpapp_dir();
@include_once "$dir/singletons/api.php";
@include_once "$dir/singletons/query.php";
@include_once "$dir/singletons/introspector.php";
@include_once "$dir/singletons/response.php";
@include_once "$dir/models/post.php";
@include_once "$dir/models/comment.php";
@include_once "$dir/models/category.php";
@include_once "$dir/models/tag.php";
@include_once "$dir/models/author.php";
@include_once "$dir/models/attachment.php";

function wpapp_init() {
  global $wpapp;

  // quick mod for secure api key, WPAPP only need the following methods

  $wpapp_options = get_option('wpapp_options');

  if ($_GET['json']) {
    if ($_GET['json']=='get_recent_posts' || $_GET['json']=='get_category_posts' || $_GET['json']=='get_page' || $_GET['json']=='get_category_index') {
      if ($_GET['apikey']!=$wpapp_options[wpapp_api_key]) {
        print "[error:99] Permission denied!";
        exit;
      }
    }
    else {
        print "[error:99] Permission denied!";
      exit;
    }
  }

  ///////

  if (phpversion() < 5) {
    add_action('admin_notices', 'wpapp_php_version_warning');
    return;
  }
  if (!class_exists('WPAPP')) {
    add_action('admin_notices', 'wpapp_class_warning');
    return;
  }
  add_filter('rewrite_rules_array', 'wpapp_rewrites');
  $wpapp = new WPAPP();
}

function wpapp_php_version_warning() {
  echo "<div id=\"wpapp-warning\" class=\"updated fade\"><p>Sorry, WPAPP requires PHP version 5.0 or greater.</p></div>";
}

function wpapp_class_warning() {
  echo "<div id=\"wpapp-warning\" class=\"updated fade\"><p>Oops, WPAPP class not found. If you've defined a WPAPP_DIR constant, double check that the path is correct.</p></div>";
}

function wpapp_activation() {
  // Add the rewrite rule on activation
  global $wp_rewrite;
  add_filter('rewrite_rules_array', 'wpapp_rewrites');
  $wp_rewrite->flush_rules();
}

function wpapp_deactivation() {
  // Remove the rewrite rule on deactivation
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}

function wpapp_rewrites($wp_rules) {
  $base = get_option('wpapp_base', 'api');
  if (empty($base)) {
    return $wp_rules;
  }
  $wpapp_rules = array(
    "$base\$" => 'index.php?json=info',
    "$base/(.+)\$" => 'index.php?json=$matches[1]'
  );
  return array_merge($wpapp_rules, $wp_rules);
}

function wpapp_dir() {
  if (defined('WPAPP_DIR') && file_exists(WPAPP_DIR)) {
    return WPAPP_DIR;
  } else {
    return dirname(__FILE__);
  }
}

function wpapp_options_page2() {
  ?>

    <div class="wrap">
      <?php screen_icon(); ?>
      <h2>WpApp</h2>
      <p>Halo</p>
    </div>
  
  <?php
}

function wpapp_options_page() {
  add_options_page('WpApp','WpApp','administrator',__FILE__, 'wpapp_options_page2');
}

// add_action('admin_menu', 'wpapp_options_page');

// Add initialization and activation hooks
// add_action('admin_menu', 'WPAPP::add_menu');
add_action('init', 'wpapp_init');
register_activation_hook("$dir/wpapp.php", 'wpapp_activation');
register_deactivation_hook("$dir/wpapp.php", 'wpapp_deactivation');

?>
