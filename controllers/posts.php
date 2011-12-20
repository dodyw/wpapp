<?php
/*
Controller name: Posts
Controller description: Data manipulation methods for posts
*/

class WPAPP_Posts_Controller {

  public function create_post() {
    global $wpapp;
    if (!current_user_can('edit_posts')) {
      $wpapp->error("You need to login with a user capable of creating posts.");
    }
    if (!$wpapp->query->nonce) {
      $wpapp->error("You must include a 'nonce' value to create posts. Use the `get_nonce` Core API method.");
    }
    $nonce_id = $wpapp->get_nonce_id('posts', 'create_post');
    if (!wp_verify_nonce($wpapp->query->nonce, $nonce_id)) {
      $wpapp->error("Your 'nonce' value was incorrect. Use the 'get_nonce' API method.");
    }
    nocache_headers();
    $post = new WPAPP_Post();
    $id = $post->create($_REQUEST);
    if (empty($id)) {
      $wpapp->error("Could not create post.");
    }
    return array(
      'post' => $post
    );
  }
  
}

?>
