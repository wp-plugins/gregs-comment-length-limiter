<?php
if( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
} else {
	   if (current_user_can('delete_plugins')) {
		   $gcll_settings = array ('gcll_upper_limit','gcll_auto_box','gcll_characters_available','gcll_override_for_admin','gcll_thank_you','gcll_thank_you_message');
		   // Nuke the options
		   echo '<div id="message" class="updated fade">';
		   foreach($gcll_settings as $setting) {
			   $delete_setting = delete_option($setting);
			   if($delete_setting) {
				   echo '<p style="color:green">';
				   printf(__('Setting \'%s\' has been deleted.', 'gcll-plugin'), "<strong><em>{$setting}</em></strong>");
				   echo '</p>';
			   } else {
				   echo '<p style="color:red">';
				   printf(__('Error deleting setting \'%s\'.', 'gcll-plugin'), "<strong><em>{$setting}</em></strong>");
				   echo '</p>';
			   }
		   }
		   echo '<strong>Thank you for using Greg&#8217;s Comment Length Limiter plugin!</strong>';
		   echo '</div>'; 
	  }
}

?>