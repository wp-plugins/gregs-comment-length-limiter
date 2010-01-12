<?php

class gcllSetupHandler {

var $plugin_prefix; // prefix for this plugin
var $options_page_details = array(); // setting up our options page

function gcllSetupHandler ($plugin_prefix,$location_full,$location_local,$options_page_details) {
$this->__construct($plugin_prefix,$location_full,$location_local,$options_page_details);
return;
} 

function __construct($plugin_prefix,$location_full,$location_local,$options_page_details) {
$this->plugin_prefix = $plugin_prefix;
$this->options_page_details = $options_page_details;
   add_filter( "plugin_action_links_{$location_local}", array(&$this,'plugin_settings_link'));
   add_action('admin_menu', array(&$this,'plugin_menu'));
   add_action('admin_init', array(&$this,'admin_init') );
   add_action('admin_head', array(&$this,'styles') );
   register_activation_hook($location_full, array(&$this,'activate') );

return;
} // end constructor

function grab_settings() {
// array keys correspond to the page of options on which the option gets handled

$options_set = array(
'default' => array(
	array("abbreviate_options", "0", 'intval'),
	array("upper_limit", "3000", 'intval'),
	array("auto_box", "1", 'intval'),
	array("characters_available", "characters available", 'wp_filter_nohtml_kses'),
	array("oversize", "0", 'intval'),
	array("override_for_admin", "0", 'intval'),
	),
'donating' => array(
	array("donated", "0", 'intval'),
	array("thank_you", "0", 'intval'),
	array("thank_you_message", "Thanks to %THIS_PLUGIN%.", 'wp_filter_nohtml_kses'),
	),
);
return $options_set;
} // end settings grabber

function activate() {
$options_set = $this->grab_settings();
$prefix = $this->plugin_prefix . '_';
foreach ($options_set as $optionset=>$optionarray) {
   foreach ($optionarray as $option) {
	 add_option($prefix . $option[0],$option[1]);
	 } // end loop over individual options
  } // end loop over options arrays
return;
}

function admin_init(){
$options_set = $this->grab_settings();
$prefix_setting = $this->plugin_prefix . '_options_';
$prefix = $this->plugin_prefix . '_';
foreach ($options_set as $optionset=>$optionarray) {
   foreach ($optionarray as $option) {
	 register_setting($prefix_setting . $optionset, $prefix . $option[0],$option[2]);
	 } // end loop over individual options
  } // end loop over options arrays
return;
}

function plugin_menu() {
$details = $this->options_page_details;
  add_options_page("{$details[0]}", "{$details[1]}", 'manage_options', "{$details[2]}");
return;
}

function plugin_settings_link($links) {
$prefix = $this->plugin_prefix;
$here = str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); // get plugin folder name
$settings = "options-general.php?page={$here}{$prefix}-options.php";
$settings_link = "<a href='{$settings}'>" . __('Settings') . '</a>';
array_unshift( $links, $settings_link );
return $links;
} // end settings link

function styles() {
$prefix = $this->plugin_prefix . '_';
echo <<<EOT
<style type="text/css">
.{$prefix}table ul {padding-top:.5em;}
.{$prefix}table th {text-align:right;}
.{$prefix}menu ul, .{$prefix}menu li {display:inline;line-height:1.8em;}
.{$prefix}menu {margin:15px 0;}
.{$prefix}menu li a {text-decoration:none;}
.{$prefix}thanks {font-style:italic;font-weight:bold;color:purple;}
.{$prefix}warning {margin:2.5em;padding:1.5em;border:1px solid red;background-color:white;}
.{$prefix}aside {float:right;margin:0 0 1em 1em;padding:.5em 1em;border:1px solid grey;width:300px;background-color:white;}
.{$prefix}aside h4 {margin-top:0;padding-top:.5em;}
ol.{$prefix}numlist {list-style-type:decimal;padding-left:2em;margin-left:0;}
.{$prefix}fine_print {font-size:.8em;font-style:italic;}
</style>
EOT;
return;
} // end admin styles

} // end class

?>