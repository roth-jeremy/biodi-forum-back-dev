<?php
/*
Plugin Name: reCAPTCHA for Asgaros Forum
Plugin URI: http://wordpress.org/plugins/recaptcha-for-asgaros-forum/
Description: Protect your Asgaros forum from spam using Googles reCAPTCHA v2. This plugin prevent bots to spam your forum and has option to enabe reCAPTCHA for guest users & logged-in users.
Author: Hitesh Chandwani
Version: 1.0
Author URI: https://hiteshchandwani.com
*/

 
$plugin = plugin_basename(__FILE__); 



add_action( 'admin_menu', 'rfaf_add_admin_submenu' );
add_action('wp_loaded', 'rfaf_save_recaptcha_setting');

add_filter("plugin_action_links_$plugin", 'rfaf_plugin_settings_link' );
add_action('asgarosforum_editor_custom_content_bottom', 'rfaf_bbp_captcha_integrate');

add_filter('asgarosforum_filter_insert_custom_validation', 'rfaf_validate_recaptcha');



function rfaf_add_admin_submenu(){

	add_submenu_page('asgarosforum-structure', 'reCAPTCHA', 'reCAPTCHA', 'read', 'asgaros-recaptcha', 'rfaf_recaptcha_callback');

}

function rfaf_recaptcha_callback(){

	require('recaptcha.php');

}

function rfaf_save_recaptcha_setting(){

	if(isset($_POST['rfaf_recaptcha_submit'])):
		$nonce = $_REQUEST['_wpnonce'];
		if(wp_verify_nonce($nonce, 'rfaf_recaptcha_submit_nonce')){
			$rfaf_recaptcha_site_key = sanitize_text_field($_POST['rfaf_recaptcha_site_key']);
			$rfaf_recaptcha_server_key = sanitize_text_field($_POST['rfaf_recaptcha_server_key']);
			$rfaf_recaptcha_registerd_user = sanitize_text_field($_POST['rfaf_recaptcha_registerd_user']);

			update_option( 'rfaf_recaptcha_site_key', $rfaf_recaptcha_site_key);
			update_option( 'rfaf_recaptcha_server_key', $rfaf_recaptcha_server_key);
			update_option( 'rfaf_recaptcha_registerd_user', $rfaf_recaptcha_registerd_user);
		}else{
			$_REQUEST['nonce_error'] = trrue;
		}
	endif;

}


function rfaf_plugin_settings_link($links){
	if(is_plugin_active('asgaros-forum/asgaros-forum.php')){

		$settings_link = '<a href="admin.php?page=asgaros-recaptcha">Enable reCAPTCHA</a>';
		array_unshift($links, $settings_link);

	}

	return $links;
}



function rfaf_bbp_captcha_integrate(){
	global $asgarosforum;
	
	$is_guest_enable = $asgarosforum->options['allow_guest_postings'];
	$site_key = get_option('rfaf_recaptcha_site_key',false);
	$server_key = get_option('rfaf_recaptcha_server_key',false);
	$enable_for_registerd_user = get_option('rfaf_recaptcha_registerd_user',false);
	
	if($server_key != false & $site_key != false & (($is_guest_enable != false and !is_user_logged_in()) or $enable_for_registerd_user!=false)){
		echo '<div class="editor-row editor-row-captcha">';
		echo "<div class='g-recaptcha' data-sitekey='$site_key'></div>";
		echo '</div>';
		wp_enqueue_script('rfaf-google-reCaptcha','https://www.google.com/recaptcha/api.js?hl=en');	
	}
	
}

function rfaf_validate_recaptcha($status){
	global $asgarosforum;

	$is_guest_enable = $asgarosforum->options['allow_guest_postings'];
	$site_key = get_option('rfaf_recaptcha_site_key',false);
	$server_key = get_option('rfaf_recaptcha_server_key',false);
	$enable_for_registerd_user = get_option('rfaf_recaptcha_registerd_user',false);

	if($server_key != false & $site_key != false & (($is_guest_enable != false and !is_user_logged_in()) or $enable_for_registerd_user!=false)){
		
		include ( plugin_dir_path( __FILE__ ) .'src/autoload.php');	
		
		$recaptcha = new \ReCaptcha\ReCaptcha($server_key);
		$gRecaptchaResponse = $_POST['g-recaptcha-response'];
		$remoteIp = $_SERVER['REMOTE_ADDR']; 


		$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp); 
		if (!$resp->isSuccess()) {
			$asgarosforum->info = __('You must enter the correct captcha.', 'asgaros-forum');
			return false;
		}

	}
	return $status;
}