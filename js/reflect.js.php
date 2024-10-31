<?php 

if (file_exists("../../../../wp-config.php")) {
	require_once("../../../../wp-config.php");
}
else {
	require_once("/Users/travis/Documents/MAMP/wordpress/wp-config.php");
}

get_currentuserinfo(); 
global $current_user;

include 'reflect.wordpress.js';

?>


