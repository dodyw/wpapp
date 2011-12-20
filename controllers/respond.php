<?php
/*
Controller name: Respond
Controller description: Comment/trackback submission methods
*/

class WPAPP_Respond_Controller {
  
  function submit_comment() {
    global $wpapp;
    nocache_headers();
    if (empty($_REQUEST['post_id'])) {
      $wpapp->error("No post specified. Include 'post_id' var in your request.");
    } else if (empty($_REQUEST['name']) ||
               empty($_REQUEST['email']) ||
               empty($_REQUEST['content'])) {
      $wpapp->error("Please include all required arguments (name, email, content).");
    } else if (!is_email($_REQUEST['email'])) {
      $wpapp->error("Please enter a valid email address.");
    }
    $pending = new WPAPP_Comment();
    return $pending->handle_submission();
  }
  
}

?>
