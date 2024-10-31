<?php
add_action('admin_menu', 'rf_plugin_menu');

function rf_plugin_menu() {

  add_options_page(__('Reflect', 'rf-settings'), __('Reflect', 'rf-settings'), 'manage_options', 'rf', 'rf_plugin_options');

}

function rf_plugin_options() {

    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
?>

<?php


    echo '<div class="wrap">';
    echo "<h2>" . __( 'Reflect Settings', 'rf-settings' ) . "</h2>";
    
    ?>

<form name="form1" method="post" action="">
<?php

    // variables for the field and option names 
    $label_name = 'Comment text selector';
    $opt_name = 'rf_comment_text_class';
    $hidden_field_name = 'has_rf_comment_text_class';
    $data_field_name = 'rf_comment_text_class';
    $description = 'A jQuery selector that identifies the DOM elements that encapsulates all of a comment\'s text. For example, \'.my_comment_text\'.';

    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
			?><div class="updated"><p><strong><?php _e('settings saved.', 'rf-settings' ); ?></strong></p></div><?php
	 }    
    _add_plugin_option($label_name, $opt_name, $hidden_field_name, $data_field_name, $description);

    // variables for the field and option names 
    $label_name = 'Enable flagging of bullets';
    $opt_name = 'rf_enable_flagging';
    $hidden_field_name = 'has_rf_enable_flagging';
    $data_field_name = 'rf_enable_flagging';
    $description = 'Set to true if you want community moderation. CURRENTLY FLAGGING DOESN\'T DO ANYTHING. Feel free to see what it looks like in the UI though.';
    
    _add_plugin_option($label_name, $opt_name, $hidden_field_name, $data_field_name, $description);
  
    
   ?>
	<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
	</p>
	
	</form>
	</div>        
   <?php
}

function _add_plugin_option( $label_name, $opt_name, $hidden_field_name, $data_field_name, $description ){

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );
        // Put an settings updated message on the screen

	}
?>
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
<p><?php _e("$label_name:", 'rf-settings' ); ?> 
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
</p>
<p><?php echo $description; ?> </p>
<hr />


<?php
 
}
