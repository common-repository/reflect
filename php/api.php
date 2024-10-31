<?php

error_log(ABSPATH);

if ( !defined('ABSPATH') ) {
  error_log("ABSPATH not defined");
  
}
require_once("../../../../wp-config.php");

wp_get_current_user();
get_currentuserinfo();

global $current_user;


if (!class_exists("ReflectBulletsAPI")) {
  class ReflectBulletsAPI {
  
    function get_data(){
      global $wpdb;
      global $current_user;

      $data = array();
      
      $comments = json_decode(str_replace('\\', '', $_GET['comments']));

      foreach ($comments as $comment_id){
        $bullets = array();
        $res = $wpdb->get_results("SELECT bullet_id, bullet_rev FROM " . $wpdb->prefix . "reflect_bullet_current WHERE comment_id = $comment_id");
        
        foreach ($res as $cur_bullet){
          $bullet = $wpdb->get_row("SELECT bullet_id as id, timestamp as ts, user as u, text as txt, rating_zen, rating_gold, rating_sun, rating_troll, rating_graffiti, rating FROM " . $wpdb->prefix . "reflect_bullet_revision WHERE id = $cur_bullet->bullet_rev");
          $bullet->ratings = array(
            'zen' => $bullet->rating_zen,
            'gold' => $bullet->rating_gold,
            'sun' => $bullet->rating_sun,
            'troll' => $bullet->rating_troll,
            'graffiti' => $bullet->rating_graffiti,
            'rating' => $bullet->rating
          );
          if ( is_user_logged_in() ){
            $db_ratings = $wpdb->get_results("SELECT bullet_id, rating FROM " . $wpdb->prefix . "reflect_rating WHERE bullet_id = $bullet->id AND user_id = $current_user->ID");
            foreach ($db_ratings as $db_rating) {
              $bullet->my_rating = $db_rating->rating;
              $bullet->ratings[$db_rating->rating] -= 1; 
            }
          }
          $highlights = $wpdb->get_results("SELECT element_id as eid FROM " . $wpdb->prefix . "reflect_highlight WHERE bullet_rev = $cur_bullet->bullet_rev");
          $bullet->highlights = array();
          foreach ($highlights as $highlight) {
            $bullet->highlights[] = $highlight->eid;
          }

          $db_response = $wpdb->get_row("SELECT response_id, response_rev FROM " . $wpdb->prefix . "reflect_response_current WHERE bullet_id = $bullet->id");

          $bullet->response = $db_response ? $wpdb->get_row("SELECT response_id as id, id as rev, timestamp as ts, user as u, text as txt, signal as sig FROM " . $wpdb->prefix . "reflect_response_revision WHERE id = $db_response->response_rev") : Null;

          $bullet->rev = $cur_bullet->bullet_rev;
          $bullets[$bullet->id] = $bullet;
        }
        $data[$comment_id] = $bullets;
      }
      
      
      $json_encoded = json_encode($data);
      return $json_encoded;
        
    }
    
    
    function delete_bullet(){
      global $wpdb;
      
      $bullet_id = $_POST['bullet_id'];
      return $wpdb->query("DELETE FROM " . $wpdb->prefix . "reflect_bullet_current WHERE bullet_id = $bullet_id");
    }       
    
    function delete_response(){
      global $wpdb;
      
      $response_id = $_POST['response_id'];
      return $wpdb->query("DELETE FROM " . $wpdb->prefix . "reflect_response_current WHERE response_id = $response_id");
    }   
    
    function add_response(){
      global $wpdb;
      global $current_user;      
      
      if (!is_user_logged_in()){
        $user = 'Anonymous';
      } else {
        $user = $current_user->display_name;
      }
      
      //$comment_id = $_POST['comment_id'];
      $bullet_id = $_POST['bullet_id'];      
      $response_text = $_POST['text'];
      if($response_text == '') return '';
      
      $signal = (int)$_POST['signal'];
      
      $modify = isset($_POST['response_id']);
      if($modify){
        //modifying existing
        $response_id = $_POST['response_id'];
        $cur_response = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "reflect_response_current WHERE response_id = $response_id");        
        //$response = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "reflect_response_revision WHERE response_rev = $cur_response->response_rev");        
      }else{
        //$res = $wpdb->get_row("SELECT id FROM " . $wpdb->prefix . "reflect_bullet WHERE comment_id = $comment_id AND text = '$bullet_text'");
        //if($res) return '';
        $response_id = (int)$wpdb->get_var( $wpdb->prepare( "SELECT MAX(response_id) FROM " . $wpdb->prefix . "reflect_response_revision" ) ) + 1;
      }
      
      //$res = $wpdb->get_row("SELECT id FROM " . $wpdb->prefix . "reflect_response WHERE bullet_id = $bullet_id AND text = '$response_text'");
      
      $params = array( 
        'response_id' => (int)$response_id, 
        'bullet_id' => (int)$bullet_id,
        'user' => $user,
        'user_id' => $current_user->ID,
        'text' => wp_kses($response_text, NULL),
        'signal' => $signal
       );
               
      $wpdb->insert( $wpdb->prefix . 'reflect_response_revision', $params );
      $response_rev = $wpdb->insert_id;
      

      if($modify){
        $wpdb->update($wpdb->prefix . 'reflect_response_current', 
                array( 'response_rev' => $response_rev ), 
                array( 'response_id' => $response_id ) );
      }else{
        $params = array(
          'response_rev' => (int)$response_rev, 
          'response_id' => (int)$response_id,
          'bullet_id' => (int)$bullet_id,
        );
               
        $wpdb->insert( $wpdb->prefix . 'reflect_response_current', $params );              
      }
        
      $resp = json_encode(array("insert_id"=>$response_id, "u"=>$user, "rev_id" => $response_rev, "sig"=>$signal));
            
      return $resp;
    }
    
    function add_bullet(){
      global $wpdb;
      global $current_user;
      
      if (!is_user_logged_in()){
        $user = 'Anonymous';
      } else {
        $user = $current_user->display_name;
      }
      
      $comment_id = $_POST['comment_id'];
      $bullet_text = $_POST['text'];
      if($bullet_text == '') return '';
      
      $modify = isset($_POST['bullet_id']);
      if($modify){
        //modifying existing
        $bullet_id = $_POST['bullet_id'];
        $cur_bullet = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "reflect_bullet_current WHERE bullet_id = $bullet_id");                
      } else {
        $bullet_id = (int)$wpdb->get_var( $wpdb->prepare( "SELECT MAX(bullet_id) FROM " . $wpdb->prefix . "reflect_bullet_revision" ) ) + 1;
      }
  
      $params = array( 
        'comment_id' => (int)$comment_id, 
        'bullet_id' => (int)$bullet_id,
        'user' => $user, 
        'text' => wp_kses($_POST['text'], NULL),
        'user_id' => $current_user->ID
      );

      $wpdb->insert( $wpdb->prefix . 'reflect_bullet_revision', $params );
      $bullet_rev = $wpdb->insert_id;
                       
      if (isset($_POST['highlights'])){
        $highlights = json_decode(str_replace('\\', '', $_POST['highlights']));
        foreach ($highlights as $value){
          $params = array( 
            'bullet_id' => $bullet_id,
            'bullet_rev' => $bullet_rev,
            'element_id' => $value, 
          );
          $wpdb->insert( $wpdb->prefix . "reflect_highlight",  $params);
        }
      }
            
      if ($modify) {
        $wpdb->update($wpdb->prefix . 'reflect_bullet_current', 
                array( 'bullet_rev' => $bullet_rev ), 
                array( 'bullet_id' => $bullet_id ) );
      } else {
        $params = array(
          'bullet_rev' => (int)$bullet_rev, 
          'comment_id' => (int)$comment_id,
          'bullet_id' => (int)$bullet_id,
        );          
               
        $wpdb->insert( $wpdb->prefix . 'reflect_bullet_current', $params );              
  
        $comment = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "comments WHERE comment_id = $comment_id");
        $post = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "posts WHERE id = $comment->comment_post_ID");
        $post_title = $post->post_title;
        $link = $post->guid;
        $comment_author = $comment->comment_author;
        $bullet_text = str_replace("\\'", "'", $bullet_text);
        try {
          $from = get_bloginfo('admin_email');
          $subject = "$user summarized a comment you wrote in \"$post_title\"";            
          $message = "Hi $comment_author,\n\n$user believes that you made the following point:\n\n\"$bullet_text\"\n\nTo verify whether this is accurate or not, please visit $link and login.\n\nThanks!";
          $headers = "From: $from" . "\r\n" .
             "Reply-To: $from" . "\r\n" .
             'X-Mailer: PHP/' . phpversion();            
          mail($comment->comment_author_email, $subject, $message, $headers);  
        } catch (Exception $e) {}
      }
      return json_encode(array("insert_id"=>$bullet_id, "u"=>$user, "rev_id" => $bullet_rev));
    }

    function post_rating() {
      global $wpdb;
      global $current_user;
      
      $comment_id = $_POST['comment_id'];
      $bullet_id = $_POST['bullet_id']; 
      $bullet_rev = $_POST['bullet_rev'];
      $rating = $_POST['rating']; 
      $is_delete = $_POST['is_delete'];
       
      $uid = $current_user->ID;
      
      #TODO: server side permission check for this operation...
      #my $commenter = $slashdb->sqlSelect('uid', 'comments', "cid = $comment_id");
      #my $summarizer = $slashdb->sqlSelect('user_id', 'reflect_bullet_revision', "id = $bullet_rev");
      #if($commenter == $uid
      #   || $user_info->{is_anon}
      #   || $summarizer == $uid ) {
      #  return "rejected";
      #}

      $wpdb->query("DELETE FROM " . $wpdb->prefix . "reflect_rating WHERE bullet_id = $bullet_id AND user_id = $uid");

      if($is_delete == 'false') {
        $rating_params = array( 
          'comment_id' => $comment_id,
          'bullet_id' => $bullet_id,
          'bullet_rev' => $bullet_rev,
          'rating' => $rating,
          'user_id' => $uid
        );
        $wpdb->insert( $wpdb->prefix . "reflect_rating",  $rating_params);
      }

      $ratings = $wpdb->get_results("SELECT rating, count(*) as cnt FROM " . $wpdb->prefix . "reflect_rating WHERE bullet_id=$bullet_id GROUP BY rating");
        
      $update_obj = array(
        'rating_zen' => 0,
        'rating_gold' => 0,
        'rating_sun' => 0,
        'rating_troll' => 0,
        'rating_graffiti' => 0
      );
      $high_cnt = 0;
      foreach ($ratings as $row) {
        $row_rating = $row->rating;
        $update_obj["rating_" . $row_rating] = $row->cnt;
        if($row->cnt > $high_cnt){
          $high_cnt = $row->cnt;
          $high_rating = $row->rating;
        }
      }
      
      $update_obj["rating"] = $high_cnt > 0 ? $high_rating : Null;


      $db_bullet = $wpdb->get_row("SELECT bullet_rev FROM " . $wpdb->prefix . "reflect_bullet_current WHERE bullet_id = $bullet_id");

      $wpdb->update($wpdb->prefix . 'reflect_bullet_revision', 
              $update_obj, 
              array( 'id' => $db_bullet->bullet_rev ) );

      $resp = json_encode(array("rating" => $high_rating, "deactivate" => false));
      if (isset($_POST['callback']))
        $resp = $_POST['callback'] + '(' + $resp + ')';


      return $resp;

    }

    function post_response(){
      try{
        if (isset($_POST['delete']) && $_POST['delete'] == 'true')
          $verb = 'delete';
        else
          $verb = 'add';
        
        if (!$this->has_permission($verb, 'response'))
          return;
        
        if ($verb == 'delete')
          $resp = $this->delete_response();
        else{
          $resp = $this->add_response();
        }
      } catch(Exception $e) {
        $resp = $e->getMessage();
      }
      
      if (isset($_POST['callback']))
        $resp = $_POST['callback'] + '(' + $resp + ')';
          
      return $resp;
    }
        
    function post_summary(){
      if (isset($_POST['delete']) && $_POST['delete'] == 'true')
        $verb = 'delete';
      else
        $verb = 'add';
      
      
      if (!$this->has_permission($verb, 'bullet'))
        return;

      if ($verb == 'delete')
        $resp = $this->delete_bullet();
      else
        $resp = $this->add_bullet();
          
      if (isset($_POST['callback']))
        $resp = $_POST['callback'] + '(' + $resp + ')';
          
      return $resp;
    }

    function has_permission($verb, $noun){
      //anons can post summaries
      //anons can't delete, unless its their own
      //no-one can post summaries of their own comments
      
      /*
       * variables
       * 
       * user_level
       * action [post bullet, delete bullet, modify bullet
       * comment author
       */
      global $wpdb;
      global $current_user;
      
      $comment_id = $_POST['comment_id'];
      $comment = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "comments WHERE comment_id = $comment_id");
      $comment_author = $comment->user_id;
      
      $bullet_id = $_POST['bullet_id'];
      $bullet = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "reflect_bullet_revision WHERE bullet_id = $bullet_id");
      $bullet_author = $bullet->user_id;
      
      if(!is_user_logged_in()) {
        $user_level = -1;
        $user = NULL;
      } else {
        $user_level = $current_user->user_level;
        $user = $current_user->ID;
      }
      
      if($noun == 'bullet'){
        if ($verb == 'delete'){
          if($bullet_author != $user && $user_level < 2){return false;}
            
        }elseif ($verb == 'add'){
          if($comment_author == $user){return false;}
        }
      }elseif($noun == 'response'){
        if($verb == 'delete'){
          if($comment_author != $user && $user_level < 2 ){return false;}                
            
        }elseif($verb == 'add'){
          if($comment_author != $user && $user_level < 2 ){return false;}                                    
        }
      }

      return true;
    }
  }
}

if (class_exists("ReflectBulletsAPI")) {
  if (!isset($reflect_api))
    $reflect_api = new ReflectBulletsAPI();

  if(!empty($_POST) && isset($_POST['operation'])){
    if ( $_POST['operation'] == 'response' ) {
      echo $reflect_api->post_response();
    } elseif ( $_POST['operation'] == 'bullet' ) {
      echo $reflect_api->post_summary();
    } elseif ( $_POST['operation'] == 'rate' ) {
      echo $reflect_api->post_rating();
    }
  } else {
    echo $reflect_api->get_data();
  }

}