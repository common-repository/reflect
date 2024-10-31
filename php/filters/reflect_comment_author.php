<?php 

if (!function_exists('add_action'))
{
   require_once("../../../../wp-config.php");
   get_currentuserinfo();
	global $current_user;
}


if (!class_exists("ReflectCommentAuthor")) {
	
class ReflectCommentAuthor {

	
	function reflect_comment_author_filter($content) {
		return "
    		<span class=rf_wp_comment_author>
        		$content
         </span>
		   ";
	}
    
}


}

if (class_exists("ReflectCommentAuthor")) {
    $reflect_comment_author = new ReflectCommentAuthor();
    add_filter('get_comment_author', array(&$reflect_comment_author, 'reflect_comment_author_filter'), 999);
}