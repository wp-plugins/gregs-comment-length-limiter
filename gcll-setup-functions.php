<?php

if (!function_exists ('is_admin')) {
   header('Status: 403 Forbidden');
   header('HTTP/1.1 403 Forbidden');
   exit();
   }

// This setup class is only loaded if we're actually on admin pages

/*
<rant>

	We now bring you a special note especially for the self-appointed thought police of WordPress plugin programming who would like everyone to believe it's just plain obvious that good plugins always store their options in a single array or object, while any plugin that adds multiple rows to the WordPress options table must have been written by an idiot...
	
	A couple of years ago, Stephen Rider wrote an excellent article about the messiness that can be created when plugin authors haphazardly add lots of rows to the WordPress options table. That article (http://striderweb.com/nerdaphernalia/2008/07/consolidate-options-with-arrays/) was GREAT, but unfortunately several subsequent authors with seemingly diminished capacity for original thought have parroted the *conclusion* (namely, that a particular example plugin would have been 'better' with a single serialized array of options) and extrapolated it into a one-size-fits-all judgement about THE RIGHT WAY OF DOING THINGS -- without ever engaging in supporting reasoning or empirical data collection. Some now routinely disparage any plugin that does not do things THEIR way, apparently without realizing how idiotic the inference is from the case of one inefficiently coded plugin to the space of all possible plugins. (This is akin to another condescension promulgated by a hyper-judgemental minority of the thought police: namely, that if a plugin doesn't employ their preferred approach to whitespace and indentation, then it must be 'bad code'.)
	
	Folks who do their own thinking about these things discover that actually, it's not at all trivial to understand how lookup time scales as more and more options are added to a serialised array (or object) or how writing time is impacted when storing an entire serialised array just to update one single option within it. It's simply screwy to ASSUME that decreasing options table rows necessarily garners ANY increase in overall efficiency, given all the extra overheads associated with reading a big chunk of serialised options just to get at some sub-set of those options or writing a big chunk just to change one or two settings. And do the thought police have any idea how much harder it is to SEARCH a database for serialised array contents? (Don't get me started on the even more spurious argument that plugins ought to store one options array in order to make it easier for blog owners to manually comb through their options tables and delete them. Ever heard of the standard WordPress uninstall hook and delete_option?)
	
	Personally, I make a lot of mistakes in my programming, I know I miss 'obvious' efficiency improvements, and in a lot of cases the manifestly 'better' way of doing things totally eludes me. But this is not one of those cases.
	
	To the thought police: if it's such an obviously good idea to mash large numbers of options into one gigantic array that must always be read and written in its entirety, why not just step up to the plate and contribute code to the WordPress core so it ALWAYS stores a single plugin's options in a single row, and stop wasting everyone's time ranting about plugin authors who don't do it your way? If register_settings can handle a whole set of plugin options in a coherent way, surely you can think of an analogous approach for abstracting away from individual option storage and retrieval? But oh, wait -- it is NOT so obvious that this is always a good idea, and maybe if it really were so obvious, then WordPress would already have been designed that way in the first place.
	
	The bottom line, IMHO... If a plugin is only using a small amount of options storage anyway, chill out: whether it uses 1 row in the options table or 5 is not a big deal either way, and surely there are more important things in life to worry about. As the number of options rows increases, though, don't presuppose to know anything at all about what is more efficient or ultimately 'better' for the database or the blog or the blog owner. If it really keeps you awake at night, how about gathering some empirical data and sharing some conclusions based on reality? Then more folks will sit up and take you more seriously. I'll certainly be among the first to revise my options handling code if anybody does come up with some real and relevant data on the topic, as distinct from mere dogmatic dumpings of derision on plugin authors who don't subscribe to the thought police code.
	
	Until then, blog owners and plugin authors have better things to do with their time.

</rant>
*/

class gcllSetupHandler {

var $plugin_prefix; // prefix for this plugin
var $options_page_details = array(); // setting up our options page

function gcllSetupHandler ($args,$options_page_details) {
$this->__construct($args,$options_page_details);
return;
} 

function __construct($args,$options_page_details) {
extract($args);
$this->plugin_prefix = $prefix;
$this->options_page_details = $options_page_details;
   add_filter( "plugin_action_links_{$location_local}", array(&$this,'plugin_settings_link'));
   add_action('admin_menu', array(&$this,'plugin_menu'));
   add_action('admin_menu', array(&$this,'wp_postbox_js'));
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
$page_hook = add_options_page("{$details[0]}", "{$details[1]}", 'manage_options', "{$details[2]}");
// NOTE: WP's system for unobtrusively inserting JS, css, etc. only on pages that are needed, documented in several places such as at http://codex.wordpress.org/Function_Reference/wp_enqueue_script appears to be broken when we're using another separate options page, so we'll have to do it the clunky way, with a URL check in the delivering function instead, and putting the add_action up in the constructor
//add_action('admin_print_scripts-' . $page_hook, array(&$this,'wp_postbox_js'));
return;
}

function pay_attention() {
// See note on plugin_menu function as to why we're doing this the crazy clunky way
$page = $this->options_page_details[2];
if (strpos(urldecode($_SERVER['REQUEST_URI']), $page) === false) return false;
else return true;
}

function wp_postbox_js() {
// See note on plugin_menu function as to why we're doing this check the crazy clunky way
if (!$this->pay_attention()) return;
wp_enqueue_script('common');
wp_enqueue_script('wp-lists');
wp_enqueue_script('postbox');
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

function styles() { // we'll need a few styles for our options pages
// See note on plugin_menu function as to why we're doing this check the crazy clunky way
if (!$this->pay_attention()) return;
$prefix = $this->plugin_prefix . '_';
echo <<<EOT
<style type="text/css">
#poststuff .inside p {font-size:1.1em;}
.{$prefix}table th {text-align:right; font-weight:bold; color:#333;}
.{$prefix}menu ul, .{$prefix}menu li {display:inline;line-height:1.8em;}
.{$prefix}menu {margin:15px 0;}
.{$prefix}menu li a {text-decoration:none;}
.{$prefix}thanks {font-style:italic;font-weight:bold;color:purple;padding:1.5em;border:1px dotted grey;}
.{$prefix}warning {margin:2.5em;padding:1.5em;border:1px solid red;background-color:white;}
.{$prefix}aside, .{$prefix}toc {float:right;margin:0 0 1em 1em;padding:.5em 1em;border:1px solid grey;width:300px;background-color:white;}
.{$prefix}toc {float:left;margin:0 1em 1em 0;width:200px;}
.{$prefix}toc ul ul {margin:.5em 0 0 1em;}
.{$prefix}aside h4, .{$prefix}toc h4 {margin-top:0;padding-top:.5em;}
ol.{$prefix}numlist {list-style-type:decimal;padding-left:2em;margin-left:0;}
.{$prefix}fine_print {font-size:.8em;font-style:italic;}
</style>
EOT;
return;
} // end admin styles

} // end class

?>