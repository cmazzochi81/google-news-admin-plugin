<?php
/**
 * @package GoogleAjax
 */
/*
Plugin Name: Google Ajax Admin Example
Plugin URI: 
Description: Displays a form for a user to enter a search term to get RSS feeds from Google News. 
Version: 1.0.0
Author: Chris A. Mazzochi
Author URI: 
License: GPLv2 or later
Text Domain: google-news-plugin
*/

//Check to see if this file is being called externally or not.  If it is 
//this ABSPATH constant will not be set. If that is the case exit the program
//immediately, perhaps an attacker is trying to gain access to the site. 
if(!defined('ABSPATH')){
	exit;
}

// enqueue scripts
function ajax_admin_enqueue_scripts( $hook ) {

	// check if our page
	if ( 'toplevel_page_ajax-admin-example' !== $hook ) return;

	// define script url
	$script_url = plugins_url( '/ajax-admin.js', __FILE__ );

	// enqueue script
	wp_enqueue_script( 'ajax-admin', $script_url, array( 'jquery' ) );

	// create nonce
	$nonce = wp_create_nonce( 'ajax_admin' );

	// define script
	$script = array( 'nonce' => $nonce );

	// localize script
	wp_localize_script( 'ajax-admin', 'ajax_admin', $script );

}
add_action( 'admin_enqueue_scripts', 'ajax_admin_enqueue_scripts' );

//Function responsible for processing the ajax request
function ajax_google_handler() {

	//Checking nonce, an alphanumeric hash appended to url 
	//so url cannot be replicated by an attacker. 
	check_ajax_referer( 'ajax_admin', 'nonce' );

	//Checking user role
	if ( ! current_user_can( 'manage_options' ) ) return;

	//Checking if the $_POST variable is set with the url and if so sanitize it. 
	$url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : false;

	//Making the GET request to the web service. 
	$response = wp_safe_remote_get($url);

	//Geting the body from the response. 
	$body = wp_remote_retrieve_body( $response );

	//Output-display the results
	echo '<pre>';

	if ( ! empty( $body ) ) {
		echo 'Response body for: '. $url . "\n\n";
		print_r( $body );
	} else {
		echo 'No results. Please check the URL and try again.';
	}

	echo '</pre>';
	// end processing
	wp_die();
}
//Ajax hook for logged-in users where wp_ajax_{action} is the name of 
//the hook and and the second parameter is the function called 
//that handles the ajax request. 
add_action( 'wp_ajax_admin_hook', 'ajax_google_handler' );


//Displays the plugin.
function ajax_google_display_form() {
	?>
	<style>
		.ajax-form-wrap { width: 100%; overflow: hidden; margin: 0 0 20px 0; }
		.ajax-form { float: left; width: 400px; }
		pre {
			width: 95%; overflow: auto; margin: 20px 0; padding: 20px;
			color: #fff; background-color: #424242;
		}
	</style>

	<h3>Return RSS</h3>
	<p>This plugin demo uses Ajax to make a request to Google News.</p>

	<div class="ajax-form-wrap">

		<form class="ajax-form" method="post">
			<p><label for="searchTerm">Enter a search term</label></p>
			<p><input id="searchTerm" name="searchTerm" type="text" class="regular-text"></p>
			<input type="submit" value="Search Google News" class="button button-primary">
		</form>
	</div>

	<div class="ajax-response"></div>

<?php

}

// add top-level administrative menu
function ajax_google_add_toplevel_menu() {

	add_menu_page(
		'Google News Web Service',
		'Google Ajax Admin',
		'manage_options',
		'ajax-admin-example',
		'ajax_google_display_settings_page',
		'dashicons-admin-generic',
		null
	);

}
add_action( 'admin_menu', 'ajax_google_add_toplevel_menu' );



// display the plugin settings page
function ajax_google_display_settings_page() {

	// check if user is allowed access
	if ( ! current_user_can( 'manage_options' ) ) return;

	?>

	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<?php echo ajax_google_display_form(); ?>
	</div>
<?php
}