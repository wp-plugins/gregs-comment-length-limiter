<?php
/*
Plugin Name: Greg's Comment Length Limiter
Plugin URI: http://gregsplugins.com/lib/plugin-details/gregs-comment-length-limiter/
Description: For WordPress 2.7 and above, this plugin displays a countdown of the remaining characters available as users enter comments on your posts, with a total comment length limit set by you.
Version: 1.5.7
Author: Greg Mulhauser
Author URI: http://gregsplugins.com/
*/

/*  Greg's Comment Length Limiter
	
	Copyright (c) 2009-2012 Greg Mulhauser
	http://gregsplugins.com
	
	Released under the GPL license
	http://www.opensource.org/licenses/gpl-license.php
	
	**********************************************************************
	This program is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
	*****************************************************************
*/

if (!function_exists ('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
	}

class gregsCommentLengthLimiter {

	var $plugin_prefix;        // prefix for options
	var $plugin_version;       // what's our version number?
	var $our_name;             // who are we?
	var $consolidate;          // whether we'll be consolidating our options into single array, or keeping discrete
	
	function gregsCommentLengthLimiter($plugin_prefix='',$plugin_version='',$our_name='',$option_style='') {
		$this->__construct($plugin_prefix,$plugin_version,$our_name,$option_style);
		return;
	} 
	
	function __construct($plugin_prefix='',$plugin_version='',$our_name='',$option_style='') {
		$this->plugin_prefix = $plugin_prefix;
		$this->plugin_version = $plugin_version;
		$this->our_name = $our_name;
		if (!empty($option_style)) $this->consolidate = ('consolidate' == $option_style) ? true : false;
		else $this->consolidate = true;
		add_action('wp_footer', array(&$this,'do_js'));
		add_action('wp_footer', array(&$this,'do_thank_you'));
		add_action('comment_form', array(&$this,'show_limit_box_wrapper'));
		add_filter('comment_form_defaults', array(&$this,'handle_tweaks_quietly'), 10, 1);
		add_filter('preprocess_comment', array(&$this,'comment_handler'), 20);
		return;
	} // end constructor
	
	// grab a setting
	function opt($name) {
		$prefix = rtrim($this->plugin_prefix, '_');
		// try getting consolidated settings
		if ($this->consolidate) $settings = get_option($prefix . '_settings');
		// is_array test will fail if settings not consolidated, isset will fail for private option not in array
		if (is_array($settings)) $value = (isset($settings[$name])) ? $settings[$name] : get_option($prefix . '_' . $name);
		// get discrete-style settings instead
		else $value = get_option($prefix . '_' . $name);
		return $value;
	} // end option retriever
	
	// grab a setting and tidy it up
	function opt_clean($name) {
		return stripslashes(wp_specialchars_decode($this->opt($name),ENT_QUOTES));
	} // end clean option retriever
	
	### Function: Check whether to override for admins
	function check_override() {
		$admin = $this->opt('override_for_admin');
		$isadmin = current_user_can('manage_options');
		if (('0' == $admin) || (!$isadmin))
			return true; // go ahead and display
		else return false;
	}
	
	### Function: Greg's Comment Length Limit JS
	function do_js() {
		if (!is_singular()) return; // don't bother unless we're on a page that could have a comment form
		if (!comments_open()) return;
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
document.getElementById('commentlen').value = $limit - textarea.value.length;
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
		if ( ( 1 == $this->opt('thank_you') ) && is_singular() ) { 
			$name = $this->our_name;
			$message = str_replace('%THIS_PLUGIN%','<a href="http://gregsplugins.com/">' . $name . '</a>',$this->opt('thank_you_message'));
			echo '<p>' . $message . '</p>';
		} 
		return;
	}

	### Function: Show the limit box, wrapper for add_action purposes
	function show_limit_box_wrapper() {
		if (did_action('comment_form_before')) return; // if this action was done, we must be using comment_form *function* under 3.0+
		$this->show_limit_box();
		return;
	}
	
	### Function: Show the limit box
	function show_limit_box($manual = '0', $spanclass = 'countdownbox', $mode = 'normal') {
		if ($this->check_override()) {
			$doshow = $this->opt('auto_box');
			if (!($doshow == $manual )) { // show only if set to auto-show and this isn't a manual call, or this is a manual call and we are not set to auto-show
				$limit = $this->opt('upper_limit');
				$available = $this->opt('characters_available');
				$boxsize = strlen(strval($limit));
				$out = <<<EOT
<span class="$spanclass">
<input readonly="readonly" type="text" id="commentlen" size="$boxsize" maxlength="$boxsize" value="$limit" style="width:auto;text-indent:0;" />
&nbsp;$available
</span>
EOT;
				if ('normal' == $mode) echo $out;
				else return $out;
			} // end check whether to display
		} // end check for admin override
		return;
	}
	
	### Function: Show the limit box manually
	function show_limit_box_manually($spanclass='countdownbox', $mode = 'normal') {
		if ('quiet' == $mode) {
			$out = $this->show_limit_box('1', $spanclass, $mode);
			return $out;
		}
		else $this->show_limit_box('1',$spanclass);
		return;
	}
	
	### Function: Handle tweaks quietly for WP 3.0+ comment_form function
	function handle_tweaks_quietly($defaults) {
		$defaults['comment_field'] = str_replace('<textarea ', '<textarea ' . $this->tweak_textarea('quiet') . ' ', $defaults['comment_field']);
		$defaults['comment_field'] .= $this->show_limit_box('0', 'countdownbox', 'quiet');
		return $defaults;
	}
	
	### Function: Tweak the textarea
	function tweak_textarea($mode = 'normal') {
		if (!$this->check_override()) return;
		$prefix = $this->plugin_prefix;
		$out = ' onkeydown="' . $prefix . 'Counter(this)" onkeyup="' . $prefix . 'Counter(this)" ';
		if ('quiet' == $mode) return $out;
		else echo $out;
		return;
	}
	
	### Function: Comment trimmer
	function comment_trimmer($totrim='',$length=500,$ellipsis='...') {
		$chr = get_option('blog_charset');
		if (mb_strlen($totrim, $chr) > $length) {
			$totrim = mb_substr($totrim, 0, $length, $chr);
			$lastdot = mb_strrpos($totrim, ".", $chr);
			$lastspace = mb_strrpos($totrim, " ", $chr);
			$shorter = mb_substr($totrim, 0, ($lastdot > $lastspace? $lastdot : $lastspace), $chr); // truncate at either last dot or last space
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
	
	### Function: Mark as moderated only if not already marked as spam by some other process
	function return_moderated($approved) {
		if ('spam' != $approved) return 0;
		else return $approved;
	}
	
	### Function: Handle comments once submitted
	function comment_handler($commentdata) {
	// first check for admin override
	if (!$this->check_override()) return $commentdata;
	// otherwise, carry on processing
	$action = $this->opt('oversize');
	$limit = $this->opt('upper_limit');
	if ((strlen($commentdata['comment_content']) > $limit) && (0 != $action) ) {
		if (1 == $action) {
			$commentdata['comment_content'] = force_balance_tags($this->comment_trimmer($commentdata['comment_content'],$limit));
		}
		elseif (2 == $action) {
			add_filter('pre_comment_approved', array(&$this,'return_moderated'));
			$this->no_sk2_please();
		}
		elseif (3 == $action) {
			add_filter('pre_comment_approved', create_function('$a', 'return \'spam\';'));
			$this->no_sk2_please();
		}
	} // end check for oversize and doing something about it
	return $commentdata;
	} // end comment handler

} // end class definition

if (is_admin()) { // only load the admin stuff if we're adminning
	include ('gcll-setup-functions.php');
	function gcll_setup_setngo() {
		$prefix = 'gcll';
		// don't use plugin_basename -- buggy when using symbolic links
		$dir = basename(dirname( __FILE__)) . '/';
		$base = basename( __FILE__);
		$location_full = WP_PLUGIN_DIR . '/' . $dir . $base;
		$location_local = $dir . $base;
		$args = compact('prefix','location_full','location_local');
		$options_page_details = array (__('Greg&#8217;s Comment Length Limiter Options', 'gcll-plugin'),__('Comment Length Limiter', 'gcll-plugin'),'gregs-comment-length-limiter/gcll-options.php');
		new gcllSetupHandler($args,$options_page_details);
		} // end setup function
	gcll_setup_setngo();
} // end admin-only stuff
else {
	$gcll_instance = new gregsCommentLengthLimiter('gcll', '1.5.7', "Greg's Comment Length Limiter");
	function gcll_tweak_textarea() {
		global $gcll_instance;
		$gcll_instance->tweak_textarea();
		return;
	} // end tweaking textarea
	function gcll_tweak_textarea_for_filtering() {
		global $gcll_instance;
		return $gcll_instance->tweak_textarea('quiet');
	} // end tweaking textarea
	function gcll_show_limit_box_manually($spanclass='countdownbox') {
		global $gcll_instance;
		$gcll_instance->show_limit_box_manually($spanclass);
		return;
	} // end show limit box manually
	function gcll_show_limit_box_for_filtering($spanclass='countdownbox') {
		global $gcll_instance;
		$out = $gcll_instance->show_limit_box('0', $spanclass, 'quiet');
		return $out;
	} // end show limit box for filtering
} // end non-admin stuff

?>