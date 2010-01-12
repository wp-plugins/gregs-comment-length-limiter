<?php
/*
Plugin Name: Greg's Comment Length Limiter
Plugin URI: http://counsellingresource.com/features/2009/02/04/comment-length-limiter-plugin/
Description: For WordPress 2.7 and above, this plugin displays a countdown of the remaining characters available as users enter comments on your posts, with a total comment length limit set by you.
Version: 1.2.6
Author: Greg Mulhauser
Author URI: http://counsellingresource.com/
*/

/*  Copyright (c) 2009 Greg Mulhauser

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 3 as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class gregsCommentLengthLimiter {

var $plugin_prefix; // prefix for option names
var $plugin_version; // what's our version number?
var $our_name; // who are we?

function gregsCommentLengthLimiter($plugin_prefix='',$plugin_version='',$our_name='') {
$this->__construct($plugin_prefix,$plugin_version,$our_name);
return;
} 

function __construct($plugin_prefix='',$plugin_version='',$our_name='') {
$this->plugin_prefix = $plugin_prefix;
$this->plugin_version = $plugin_version;
$this->our_name = $our_name;
add_action('wp_footer', array(&$this,'do_js'));
add_action('wp_footer', array(&$this,'do_thank_you'));
add_action('comment_form', array(&$this,'show_limit_box_wrapper'));
add_filter('preprocess_comment', array(&$this,'comment_handler'));
return;
} // end constructor


function opt($name) {
return get_option($this->plugin_prefix . '_' . $name);
} // end option retriever

function opt_clean($name) {
return stripslashes(wp_specialchars_decode($this->opt($name),ENT_QUOTES));
} // end clean option retriever

### Function: Check whether to override for admins
function check_override() {
$admin = $this->opt('override_for_admin');
$isadmin = current_user_can('manage_options');
if (($admin == '0') || (!$isadmin))
   return true; // go ahead and display
else return false;
}

### Function: Greg's Comment Length Limit JS
function do_js() {
   if ($this->check_override()) {
   $limit = $this->opt('upper_limit');
   $name = $this->our_name;
   $version = $this->plugin_version;
   $prefix = $this->plugin_prefix;
echo <<<EOT
<!-- Start Of Script Generated By {$name} Plugin {$version} -->
<script type="text/javascript">
<!--
function {$prefix}Counter(textarea) {
if (textarea.value.length > $limit)
textarea.value = textarea.value.substring(0, $limit);
else
document.commentform.commentLen.value = $limit - textarea.value.length;
}
//-->
</script>
<!-- End of Script Generated By {$name} Plugin {$version} -->
EOT;
   } // end of check for admin override
return;
} 
### Function: Thank you
function do_thank_you() {
  if (($this->opt('thank_you') == 1) && is_single() ){ 
	$name = $this->our_name;
	$message = str_replace('%THIS_PLUGIN%','<a href="http://counsellingresource.com/">' . $name . '</a>',$this->opt('thank_you_message'));
	echo '<p>' . $message . '</p>';
   } 
  return;
}
### Function: Show the limit box, wrapper for add_action purposes
function show_limit_box_wrapper() {
$this->show_limit_box();
return;
}
### Function: Show the limit box
function show_limit_box($manual='0',$spanclass='countdownbox') {
if ($this->check_override()) {
   $doshow = $this->opt('auto_box');
   if (!($doshow == $manual )) { // show only if set to auto-show and this isn't a manual call, or this is a manual call and we are not set to auto-show
	  $limit = $this->opt('upper_limit');
	  $available = $this->opt('characters_available');
	  $boxsize = strlen(strval($limit));
echo <<<EOT
<span class="$spanclass">
<input readonly="readonly" type="text" name="commentLen" size="$boxsize" maxlength="$boxsize" value="$limit" style="width:auto;" />
&nbsp;$available
</span>
EOT;
	  } // end check whether to display
   } // end check for admin override
return;
}
### Function: Show the limit box manually
function show_limit_box_manually($spanclass='countdownbox') {
$this->show_limit_box('1',$spanclass);
return;
}

### Function: Tweak the textarea
function tweak_textarea() {
if ($this->check_override()) {
   $prefix = $this->plugin_prefix;
   $limit = $this->opt('upper_limit');
   echo ' onkeydown="' . $prefix . 'Counter(this)" onkeyup="' . $prefix . 'Counter(this)" ';
   } // end check for admin override
return;
}

### Function: Comment trimmer
function comment_trimmer($totrim='',$length=500,$ellipsis='...') {
if (strlen($totrim) > $length) {
	$totrim = substr($totrim, 0, $length);
	$lastdot = strrpos($totrim, ".");
	$lastspace = strrpos($totrim, " ");
	$shorter = substr($totrim, 0, ($lastdot > $lastspace? $lastdot : $lastspace)); // truncate at either last dot or last space
	$shorter = rtrim($shorter, ' .') . $ellipsis; // trim off ending periods or spaces and append ellipsis
	} // end of snipping when too long
	else { $shorter = $totrim; }
return $shorter;
} // end grm_trimmer

### Function: Stop SK2 approving oversized comment if we are moderating or flagging as spam
function no_sk2_please() {
	if (function_exists('sk2_fix_approved')) {
	remove_filter('pre_comment_approved', 'sk2_fix_approved');
	remove_action('comment_post', 'sk2_filter_comment');
	}
return;
} // end no_sk2_please


### Function: Handle comments once submitted
function comment_handler($commentdata) {
if ($this->check_override()) {
$action = $this->opt('oversize');
$limit = $this->opt('upper_limit');
if ((strlen($commentdata['comment_content']) > $limit) && ($action != 0) ) {
	if ($action == 1) $commentdata['comment_content'] = $this->comment_trimmer($commentdata['comment_content'],$limit);
	elseif ($action == 2) {
	add_filter('pre_comment_approved', create_function('$a', 'return \'0\';'));
	$this->no_sk2_please();
	}
	elseif ($action == 3) {
	add_filter('pre_comment_approved', create_function('$a', 'return \'spam\';'));
	$this->no_sk2_please();
	}
	} // end check for oversize and doing something about it
} // end check for admin override
return $commentdata;
} // end comment handler

} // end class definition

if (is_admin()) {
   include ('gcll-setup-functions.php');
   function gcll_setup_setngo() {
	  $prefix = 'gcll';
	  $location_full = __FILE__;
	  $location_local = plugin_basename(__FILE__);
	  $options_page_details = array (__('Greg&#8217;s Comment Length Limiter Options', 'gcll-plugin'),__('Comment Length Limiter', 'gcll-plugin'),'gregs-comment-length-limiter/gcll-options.php');
	  new gcllSetupHandler($prefix,$location_full,$location_local,$options_page_details);
	  } // end setup function
   gcll_setup_setngo();
   } // end admin-only stuff
else
   {
   $gcll_instance = new gregsCommentLengthLimiter('gcll', '1.2.6', "Greg's Comment Length Limiter");
   function gcll_tweak_textarea() {
	  global $gcll_instance;
	  $gcll_instance->tweak_textarea();
	  return;
	  } // end tweaking textarea
   function gcll_show_limit_box_manually($spanclass='countdownbox') {
	  global $gcll_instance;
	  $gcll_instance->show_limit_box_manually($spanclass);
	  return;
	  } // end show limit box manually
   } // end non-admin stuff

?>