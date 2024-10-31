<?php

function reflect_bullets_current() {
  global $wpdb;
  $table_name = "reflect_bullet_current";

  $sql = "id mediumint(9) NOT NULL AUTO_INCREMENT,

      bullet_id mediumint(9),
      comment_id mediumint(9),
      bullet_rev mediumint(9),
	      
		  PRIMARY KEY id (id),
      FOREIGN KEY (comment_id) REFERENCES " . $wpdb->prefix . "comments(comment_ID) ON DELETE SET NULL ON UPDATE CASCADE,
      FOREIGN KEY (bullet_id) REFERENCES " . $wpdb->prefix . "reflect_bullets_revision(bullet_id) ON DELETE CASCADE ON UPDATE CASCADE,
      FOREIGN KEY (bullet_rev) REFERENCES " . $wpdb->prefix . "reflect_bullets_revision(id) ON DELETE CASCADE ON UPDATE CASCADE";

  return array( "table_name" => $table_name, "sql" => $sql  );     
}

function reflect_bullets_revision() {
   global $wpdb;
   $table_name = "reflect_bullet_revision";

   $sql = "id mediumint(9) NOT NULL AUTO_INCREMENT,
		  
		  bullet_id mediumint(9) NOT NULL,
		  
		  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  user tinytext NOT NULL,
      user_id mediumint(9),
      comment_id mediumint(9),
	      
      text text NOT NULL,

      rating tinytext,        
      rating_zen mediumint(9), 
      rating_gold mediumint(9), 
      rating_sun mediumint(9), 
      rating_troll mediumint(9), 
      rating_graffiti mediumint(9), 
		  
		  PRIMARY KEY id (id),
      FOREIGN KEY (comment_id) REFERENCES " . $wpdb->prefix . "comments(comment_ID) ON DELETE SET NULL ON UPDATE CASCADE,
      FOREIGN KEY (user_id) REFERENCES " . $wpdb->prefix . "users(ID) ON DELETE SET NULL ON UPDATE CASCADE";

  return array( "table_name" => $table_name, "sql" => $sql  );    

}

function reflect_highlights() {
   global $wpdb;
   $table_name = "reflect_highlight";

   $sql = "id mediumint(9) NOT NULL AUTO_INCREMENT,
		  element_id tinytext NOT NULL,
		  bullet_id mediumint(9),
		  bullet_rev mediumint(9),
		  
		  PRIMARY KEY id (id),
      FOREIGN KEY (bullet_id) REFERENCES " . $wpdb->prefix . "reflect_bullets_revision(bullet_id) ON DELETE CASCADE ON UPDATE CASCADE,
      FOREIGN KEY (bullet_rev) REFERENCES " . $wpdb->prefix . "reflect_bullets_revision(id) ON DELETE CASCADE ON UPDATE CASCADE";
  return array( "table_name" => $table_name, "sql" => $sql  );   
   		
}

function reflect_ratings() {
  global $wpdb;
  $table_name = "reflect_rating";

  $sql = "id mediumint(9) NOT NULL AUTO_INCREMENT,
    bullet_id mediumint(9),
    bullet_rev mediumint(9),
    comment_id mediumint(9),
    user_id mediumint(9),
    rating tinytext,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY id (id),
    FOREIGN KEY (bullet_id) REFERENCES " . $wpdb->prefix . "reflect_bullets_revision(bullet_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (bullet_rev) REFERENCES " . $wpdb->prefix . "reflect_bullets_revision(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (comment_id) REFERENCES " . $wpdb->prefix . "comments(comment_ID) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES " . $wpdb->prefix . "users(ID) ON DELETE SET NULL ON UPDATE CASCADE";
  return array( "table_name" => $table_name, "sql" => $sql  );   

}

function reflect_response_current() {
   global $wpdb;
   $table_name = "reflect_response_current";

   $sql = "id mediumint(9) NOT NULL AUTO_INCREMENT,
		  bullet_id mediumint(9),
		  response_id mediumint(9),
		  response_rev mediumint(9),
		  
		  PRIMARY KEY id (id),
      FOREIGN KEY (bullet_id) REFERENCES " . $wpdb->prefix . "reflect_bullets_current(bullet_id) ON DELETE CASCADE ON UPDATE CASCADE,
      FOREIGN KEY (response_id) REFERENCES " . $wpdb->prefix . "reflect_response_revision(response_id) ON DELETE CASCADE ON UPDATE CASCADE,
      FOREIGN KEY (response_rev) REFERENCES " . $wpdb->prefix . "reflect_response_revision(id) ON DELETE CASCADE ON UPDATE CASCADE";
   		
  return array( "table_name" => $table_name, "sql" => $sql  );  

}

function reflect_response_revision() {
   global $wpdb;
   $table_name = "reflect_response_revision";

   $sql = "id mediumint(9) NOT NULL AUTO_INCREMENT,

		  response_id mediumint(9) NOT NULL,		  
		  bullet_id mediumint(9) NOT NULL,

		  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  user tinytext NOT NULL,
      user_id mediumint(9),
	      
      signal mediumint(9),
	      
      text text NOT NULL,
		  		  
		  PRIMARY KEY id (id),
      FOREIGN KEY (bullet_id) REFERENCES " . $wpdb->prefix . "reflect_bullets_revision(bullet_id) ON DELETE CASCADE ON UPDATE CASCADE,
      FOREIGN KEY (user_id) REFERENCES " . $wpdb->prefix . "users(ID) ON DELETE SET NULL ON UPDATE CASCADE";
   		
  return array( "table_name" => $table_name, "sql" => $sql  );  

}


?>