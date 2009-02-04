<?php
/*
	 WordPress 2.7 Plugin: Greg's Comment Length Limiter 1.0
	 Copyright (c) 2009 Greg Mulhauser
	 
	 File Written By:
	 - Greg Mulhauser
	 - http://counsellingresource.com
	 - http://mulhauser.net
	 
	 Information:
	 - Greg's Comment Length Limiter Options Page
	 - wp-content/plugins/gregs-comment-length-limiter/gcll-options.php
*/

$site_link = ' <a href="http://counsellingresource.com/">CounsellingResource.com</a>';
$plugin_page = ' <a href="http://counsellingresource.com/features/2009/02/04/comment-length-limiter-plugin/">Greg&#8217;s Comment Length Limiter plugin</a>';

?>

<div class="wrap">
<form method="post" action="options.php"> 
<?php settings_fields('gcll_options'); ?>
<?php screen_icon(); ?>
<h2><?php _e('Greg&#8217;s Comment Length Limiter Settings and Usage', 'gcll-plugin'); ?></h2>
<p><?php _e('For usage instructions, please see the README file distributed with this plugin, and for more details please see the plugin page at:', 'gcll-plugin'); echo $plugin_page; ?>.</p>
<h3><?php _e('Upper Length Limit', 'gcll-plugin'); ?></h3>
<p><?php _e('Greg&#8217;s Comment Length Limiter plugin allows you to specify the maximum length of comments accepted in the comment box.', 'gcll-plugin'); ?></p>
<table class="form-table">
  <tr>
	  <th scope="row" valign="top"><?php _e('Upper Limit', 'gcll-plugin'); ?></th>
	  <td>
			  <input type="text" size="10" name="gcll_upper_limit" value="<?php echo get_option('gcll_upper_limit'); ?>" /><br /><?php _e('Total number of characters', 'gcll-plugin'); ?>
	  </td> 
  </tr>
</table>
<h3><?php _e('Inserting the Character Countdown Box', 'gcll-plugin'); ?></h3>
<p><?php printf(__('Greg&#8217;s Comment Length Limiter plugin can automatically insert a box below the comment form textarea which lets the user know how many characters they have left before reaching the limit. Alternatively, if you&#8217;d like to position the box yourself, you can insert it manually by adding %s somewhere within your comment form.', 'gcll-plugin'), '<code>&lt;?php gcll_show_limit_box_manually(); ?></code>'); ?></p>
<p><?php printf(__('You don&#8217;t %s to display the countdown box at all, but if you don&#8217;t, your users will probably be puzzled when they reach the limit and are not allowed to enter any more text.', 'gcll-plugin'), '<em>have</em>'); ?></p>
<table class="form-table">
  <tr>
	  <th scope="row" valign="top"><?php _e('Automatically Show Countdown Box?', 'gcll-plugin'); ?></th>
	  <td>
	  <ul>
		  <li><input type="radio" name="gcll_auto_box" value="1"<?php checked('1', get_option('gcll_auto_box')); ?> /> <?php _e('Yes - Automatically Insert the Box', 'gcll-plugin'); ?></li>
		  <li><input type="radio" name="gcll_auto_box" value="0"<?php checked('0', get_option('gcll_auto_box')); ?> /> <?php _e('No - I Will Add the Box to My Theme Myself', 'gcll-plugin'); ?></li>
	  </ul>
	  </td> 
  </tr>
  <tr>
	  <th scope="row" valign="top"><?php _e('Text to Display After the Countdown Box', 'gcll-plugin'); ?></th>
	  <td>
			  <input type="text" size="40" name="gcll_characters_available" value="<?php echo get_option('gcll_characters_available'); ?>" /><br /><?php _e('(Recommended: &#8216;characters available&#8217;)', 'gcll-plugin'); ?>
	  </td> 
  </tr>
</table>
<h3><?php _e('Disabling Length Limits for Adminstrators', 'gcll-plugin'); ?></h3>
<table class="form-table">
  <tr>
	  <th scope="row" valign="top"><?php _e('Disable for Administrators?', 'gcll-plugin'); ?></th>
	  <td>
	  <ul>
		  <li><input type="radio" name="gcll_override_for_admin" value="1"<?php checked('1', get_option('gcll_override_for_admin')); ?> /> <?php _e('Yes - Let Adminstrators Ramble at Will', 'gcll-plugin'); ?></li>
		  <li><input type="radio" name="gcll_override_for_admin" value="0"<?php checked('0', get_option('gcll_override_for_admin')); ?> /> <?php _e('No - Administrators Will Be Limited Too', 'gcll-plugin'); ?></li>
	  </ul>
	  </td> 
  </tr>
</table>
<h3><?php _e('Hat Tip?', 'gcll-plugin'); ?></h3>
<p><?php _e('If you feel that Greg&#8217;s Comment Length Limiter plugin has improved your blog&#8217;s comments section, you can choose to display a small thank you message in the footer. This is NOT ENABLED by default, but you can enable it here:', 'gcll-plugin'); ?></p>
<table class="form-table">
  <tr>
	  <th scope="row" valign="top"><?php _e('Display Thank You Message?', 'gcll-plugin'); ?></th>
	  <td>
		  <ul>
			  <li><input type="radio" name="gcll_thank_you" value="0"<?php checked('0', get_option('gcll_thank_you')); ?> /> <?php _e('No - do not add anything to my footer', 'gcll-plugin'); ?></li>
			  <li><input type="radio" name="gcll_thank_you" value="1"<?php checked('1', get_option('gcll_thank_you')); ?> /> <?php _e('Yes - display a thank you message as specified below', 'gcll-plugin'); ?></li>
		  </ul>
	  </td> 
  </tr>
  <tr>
	  <th scope="row" valign="top"><?php _e('Message to Display (only if selected above):', 'gcll-plugin'); ?></th>
	  <td>
			  <input type="text" size="40" name="gcll_thank_you_message" value="<?php echo get_option('gcll_thank_you_message'); ?>" /><br /><?php printf(__('(The text %s will be replaced with the name and link to the plugin.)', 'gcll-plugin'), '<strong>%THIS_PLUGIN%</strong>'); ?>
	  </td> 
  </tr>
  <tr>
  <th></th>
  <td>   <p class="submit">
  <input type="submit" name="Submit" class="button" value="<?php _e('Save Changes', 'gcll-plugin'); ?>" />
</p>
</td>
</tr>
</table>
</form>
  <h3><?php _e('Usage', 'gcll-plugin'); ?></h3>
<p><?php _e('Please also see the README file distributed with this plugin, and for details on how to wrap functions in conditionals so your theme will only rely on this plugin when it is activated, please see the plugin page at: ', 'gtcn-plugin'); echo $plugin_page; ?>.</p>
<ul style="list-style-type:disc;padding-left:1.5em;">
	<li><?php printf(__('(Make sure your comment form has a name attribute of %s.)', 'gcll-plugin'), '<strong>commentform</strong>'); ?></li>
	<li><?php _e('Add the following function call inside the textarea tag for your comment area, preferably wrapped in a conditional that tests whether the function exists:', 'gcll-plugin'); echo ' <code>&lt;?php gcll_tweak_textarea(); ?></code>'; ?></li>
	<li><?php _e('Optionally, if you would like to position the countdown box yourself, add the following function call wherever you would like the box to appear within your comment form, again preferably wrapped in a conditional that tests whether the function exists:', 'gcll-plugin'); echo ' <code>&lt;?php gcll_show_limit_box_manually(); ?></code>'; ?></li>
</ul>
			
<h3><?php _e('Supporting Plugin Development', 'gcll-plugin'); ?></h3>
<p><?php _e('If you find this plugin useful, please consider telling your friends with a quick post about it and/or a mention of our site:', 'gcll-plugin'); echo $site_link; ?>.</p>
<p><?php _e('And of course, donations of any amount via PayPal won&#8217;t be refused! Please see the plugin page for details:', 'gcll-plugin'); echo $plugin_page; ?>.</p>
<p><em><?php _e('Thank you!', 'gcll-plugin'); ?></em></p>
<h3><?php _e('Fine Print', 'gcll-plugin'); ?></h3>
<p style="font-size:.8em"><em><?php _e('This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License version 3 as published by the Free Software Foundation.', 'gcll-plugin'); ?></em></p>
<p style="font-size:.8em"><em><?php _e('This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.', 'gcll-plugin'); ?></em></p>
<p>&nbsp;</p>
</div>
<?php
?>