<?php
/*
Plugin Name: Reflect (for comments)
Plugin URI: http://wordpress.org/extend/plugins/reflect/
Description: Crowdsourced comment summarization. Helps people listen. Helps everyone find the useful points. 
Version: 0.2.0
Author: Travis Kriplean
Author URI: http://www.cs.washington.edu/homes/travis/
License: GPL2
*/

/*  Copyright 2010  Travis Kriplean  (email : kriplean@u.washington.edu)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function rf_init() {
  if (!is_admin()) {
    
  	$siteurl = get_option('siteurl');
  	
    wp_deregister_script('jquery');
    wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js', false, '1.6.4', true);
    
    wp_enqueue_script('jqueryui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js', array('jquery'));
    wp_enqueue_script('dependencies', $siteurl . '/wp-content/plugins/reflect/js/third_party/reflect_dependencies.js.php', array('jquery'));
    wp_enqueue_script('reflect', $siteurl . '/wp-content/plugins/reflect/js/reflect.js', array('jquery'));
    
    wp_enqueue_script('reflect.wordpress', $siteurl . '/wp-content/plugins/reflect/js/reflect.js.php', array('jquery'));
  }		
}


add_action('init', 'rf_init');

function register_head() {
  
     $siteurl = get_option('siteurl');
     $url = $siteurl . '/wp-content/plugins/reflect/css/reflect.css';
     echo "<link rel=stylesheet type=text/css href=$url />";
     
     $url = $siteurl . '/wp-content/plugins/reflect/css/reflect_wordpress.css';
     echo "<link rel=stylesheet type=text/css href=$url />";
     
     $url = $siteurl . '/wp-content/plugins/reflect/css/reflect.wordpress.'. get_current_theme() . '.css';
     echo "<link rel=stylesheet type=text/css href=$url />";
     
     $url = $siteurl . '/wp-content/plugins/reflect/css/jquery.ui.css';
     echo "<link rel=stylesheet type=text/css href=$url />";
     
}

add_action('wp_head', 'register_head');

//include 'php/filters/reflect_comment_text.php';
require 'php/filters/reflect_comment_author.php';
require 'php/models.php';
require 'php/options.php';

$reflect_db_version = "1.4";
function update_or_activate() {
  $reflect_db_version = "1.4";
  $installed_ver = get_option( "reflect_db_version" );
  
  $tables = array( 
    reflect_bullets_current(),
    reflect_bullets_revision(),
    reflect_highlights(),
    reflect_response_current(),
    reflect_response_revision(),
    reflect_ratings()
  );

  foreach ($tables as $table_def) {
    _create_table($table_def["table_name"], $table_def["sql"], $installed_ver, $reflect_db_version);
  }
  if (!$installed_ver) {
    error_log("adding reflect_db_version option $reflect_db_version");
    update_option("reflect_db_version", $reflect_db_version);
  } elseif( $installed_ver != $reflect_db_version ) {
    error_log("updating reflect_db_version option to $reflect_db_version");
    migrate($installed_ver);
    update_option( "reflect_db_version", $reflect_db_version );
  }
}

function reflect_set_default_options() {
  $options = array(
    'rf_comment_text_class' => '.comment-body > p',
    'rf_enable_flagging' => 'true'
  );

  foreach ($options as $option => $default) {
    $curval = get_option($option);
    if ( !$curval || $curval == '' ) {    
      update_option( $option, $default );
    }
  }

}

function migrate($from_version) {
  global $wpdb;
  $from_version = floatval($from_version);
  
  if (!$from_version || $from_version <= 1.2) {
    $wpdb->query("UPDATE " . $wpdb->prefix . "reflect_bullet_revision rf, " . $wpdb->prefix . "users u SET rf.user_id=u.ID, rf.user=u.display_name WHERE rf.user=u.user_login");

    $wpdb->query("UPDATE " . $wpdb->prefix . "reflect_response_revision rf, " . $wpdb->prefix . "users u SET rf.user_id=u.ID, rf.user=u.display_name WHERE rf.user=u.user_login");
    
  }
  if ($from_version <= 1.3) {
    

  }
}

function _create_table($table_name, $sql, $installed_ver, $latest_ver) {
  global $wpdb;

  if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name || $installed_ver != $latest_ver ) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta("CREATE TABLE " . $wpdb->prefix . $table_name . "  (  " . $sql . "  )  ;");
  } 
}

register_activation_hook(__FILE__,'update_or_activate'); 
register_activation_hook(__FILE__,'reflect_set_default_options');
 
// handle plugin update...http://wpdevel.wordpress.com/2010/10/27/plugin-activation-hooks/
migrate("1.2");
$installed_ver = get_option( "reflect_db_version" );
if ( $installed_ver && $installed_ver != get_reflect_version()) {
  error_log('Updating...');	
  update_or_activate();
}
    
?>
