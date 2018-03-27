<?php

// Variables

$client_id  = ''; // Enter the client ID provided by stampd.io
$secret_key = ''; // Enter the secret key provided by stampd.io
$blockchain = ''; // BTC, ETH, BCH, DASH, FCT
$hash       = ''; // Enter your hash

// Functions

/*
 * Perform POST cURL
 *
 * $url string API URL
 * $fields array Fields that will be converted into URL params
 */
function stampd_perform_post_curl( $url, $fields = array() ) {
	$fields_string = '';

	foreach ( $fields as $key => $value ) {
		$fields_string .= $key . '=' . $value . '&';
	}
	$fields_string = rtrim( $fields_string, '&' );

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POST, count( $fields ) );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );
	$res = curl_exec( $ch );
	curl_close( $ch );

	return json_decode( $res );
}

/*
 * Perform plain get contents
 *
 * $url string API URL
 */
function stampd_perform_get_contents( $url ) {
	$res = file_get_contents( $url );

	return json_decode( $res );
}

/*
 * API Auth call
 *
 * $auth_url string API URL
 * $client_id string Client ID
 * $secret_key string Secret key
 */
function stampd_api_auth_call( $auth_url, $client_id, $secret_key ) {
	$url = $auth_url . '/init?client_id=' . $client_id . '&secret_key=' . $secret_key;

	return stampd_perform_get_contents( $url );
}

// Auth

$url           = 'https://stampd.io/api/v2';
$auth_response = stampd_api_auth_call( $url, $client_id, $secret_key );

// Check auth success

if ( is_object( $auth_response ) && property_exists( $auth_response, 'code' ) && $auth_response->code === 300 ) {
	// retrieve the session ID required for auth for other commands
	$session_id = $auth_response->session_id;

	// Post hash
	$fields = array(
		'requestedURL' => '/hash',
		'force_method' => 'POST', // method can also be forced via a parameter
		'session_id'   => $session_id, // new param name: sess_id
		'blockchain'   => $blockchain,
		'hash'         => $hash,
//		'meta_emails'   => $email,
//		'meta_notes'    => $notes,
//		'meta_filename' => $filename,
//		'meta_category' => ! stampd_empty( $cat_obj ) ? $cat_obj->name : null,
	);

	$post_response = stampd_perform_post_curl( $url . '.php', $fields );

	var_dump( $post_response );

} else {
	die( 'API Authentication failed.' );
}