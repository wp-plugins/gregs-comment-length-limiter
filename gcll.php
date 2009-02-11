<?php
/*
Plugin Name: Greg's Comment Length Limiter
Plugin URI: http://counsellingresource.com/features/2009/02/04/comment-length-limiter-plugin/
Description: For WordPress 2.7 and above, this plugin displays a countdown of the remaining characters available as users enter comments on your posts, with a total comment length limit set by you.
Version: 1.1.1
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
function gcll_init() {
add_option("gcll_upper_limit", "3000");
add_option("gcll_auto_box", "0");
add_option("gcll_characters_available", "characters available");
add_option("gcll_oversize", "0");
add_option("gcll_override_for_admin", "0");
add_option("gcll_thank_you", "0");
add_option("gcll_thank_you_message", "Thanks to %THIS_PLUGIN%.");
}
function gcll_admin_init(){
register_setting('gcll_options', 'gcll_upper_limit', 'intval');
register_setting('gcll_options', 'gcll_auto_box', 'intval');
register_setting('gcll_options', 'gcll_characters_available', 'wp_filter_nohtml_kses');
register_setting('gcll_options', 'gcll_oversize', 'intval');
register_setting('gcll_options', 'gcll_override_for_admin', 'intval');
register_setting('gcll_options', 'gcll_thank_you', 'intval');
register_setting('gcll_options', 'gcll_thank_you_message', 'wp_filter_nohtml_kses');
}
function gcll_menu() {
add_options_page(__('Comment Length Limiter Options', 'gcll-plugin'), __('Comment Length Limiter', 'gcll-plugin'), 'manage_options', 'gregs-comment-length-limiter/gcll-options.php') ;
}
### Function: Check whether to override for admins
function gcll_check_override() {
$admin = get_option('gcll_override_for_admin');
$isadmin = current_user_can('manage_options');
if (($admin == '0') || (!$isadmin))
return true; 
else return false;
}
### Function: Greg's Comment Length Limit JS
function gcll_js() {
if (gcll_check_override()) {
$limit = get_option('gcll_upper_limit');
echo <<<EOT
<!-- Start Of Script Generated By Greg's Comment Length Limiter Plugin 1.1.1 -->
<script type="text/javascript">
<!--
function gcllCounter(textarea) {
if (textarea.value.length > $limit)
textarea.value = textarea.value.substring(0, $limit);
else
document.commentform.commentLen.value = $limit - textarea.value.length;
}
//-->
</script>
<!-- End of Script Generated By Greg's Comment Length Limiter Plugin 1.1.1 -->
EOT;
} 
return;
} 
### Function: Thank you
function gcll_thanks() {
if ((get_option('gcll_thank_you') == 1) && is_single() ){ 
$message = str_replace('%THIS_PLUGIN%','<a href="http://counsellingresource.com/">Greg&#8217;s Comment Length Limiter plugin</a>',get_option('gcll_thank_you_message'));
echo '<p>' . $message . '</p>';
} 
return;
}
### Function: Show the limit box, wrapper for add_action purposes
function gcll_show_limit_box_wrapper() {
gcll_show_limit_box();
return;
}
### Function: Show the limit box
function gcll_show_limit_box($manual='0',$spanclass='countdownbox') {
if (gcll_check_override()) {
$doshow = get_option('gcll_auto_box');
if (!($doshow == $manual )) { 
$limit = get_option('gcll_upper_limit');
$available = get_option('gcll_characters_available');
$boxsize = strlen(strval($limit));
echo <<<EOT
<span class="$spanclass">
<input readonly="readonly" type="text" name="commentLen" size="$boxsize" maxlength="$boxsize" value="$limit" style="width:auto;" />
&nbsp;$available
</span>
EOT;
} 
} 
return;
}
### Function: Show the limit box manually
function gcll_show_limit_box_manually($spanclass='countdownbox') {
gcll_show_limit_box('1',$spanclass);
return;
}
### Function: Tweak the textarea
function gcll_tweak_textarea() {
if (gcll_check_override()) {
$limit = get_option('gcll_upper_limit');
echo ' onkeydown="gcllCounter(this)" onkeyup="gcllCounter(this)" ';
} 
return;
}
### Function: Comment trimmer
function gcll_comment_trimmer($totrim='',$length=500,$ellipsis='...') {
if (strlen($totrim) > $length) {
$totrim = substr($totrim, 0, $length);
$lastdot = strrpos($totrim, ".");
$lastspace = strrpos($totrim, " ");
if (!(($lastdot === false) && ($lastspace === false))) {
$shorter = substr($totrim, 0, ($lastdot > $lastspace? $lastdot : $lastspace)); 
$shorter = rtrim($shorter, ' .') . $ellipsis; 
} 
else { $shorter = $totrim . $ellipsis; } 
} 
else { $shorter = $totrim; }
return $shorter;
} 
### Function: Stop SK2 approving oversized comment if we are moderating or flagging as spam
function gcll_no_sk2_please() {
if (function_exists('sk2_fix_approved')) {
remove_filter('pre_comment_approved', 'sk2_fix_approved');
remove_action('comment_post', 'sk2_filter_comment');
}
return;
}
### Function: Handle comments once submitted
function gcll_comment_handler($commentdata) {
if (gcll_check_override()) {
$action = get_option('gcll_oversize');
$limit = get_option('gcll_upper_limit');
if ((strlen($commentdata['comment_content']) > $limit) && ($action != 0) ) {
if ($action == 1) $commentdata['comment_content'] = gcll_comment_trimmer($commentdata['comment_content'],$limit);
elseif ($action == 2) {
add_filter('pre_comment_approved', create_function('$a', 'return \'0\';'));
gcll_no_sk2_please();
}
elseif ($action == 3) {
add_filter('pre_comment_approved', create_function('$a', 'return \'spam\';'));
gcll_no_sk2_please();
}
}
}
return $commentdata;
}
add_action('admin_menu', 'gcll_menu');
add_action('admin_init', 'gcll_admin_init' );
register_activation_hook( __FILE__, 'gcll_init' );
add_action('wp_footer', 'gcll_js');
add_action('wp_footer', 'gcll_thanks');
add_action('comment_form', 'gcll_show_limit_box_wrapper');
add_filter('preprocess_comment', 'gcll_comment_handler');
?>