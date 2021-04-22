<?php
/**
 * Page Settings
 * These functions can be used within themes or external resources
 * 
 * @package Hammock/Functions
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get page links within the account page
 * 
 * @param string $endpoint - the endpoint
 * @param string $value - optional value
 * 
 * @since 1.0.0
 * 
 * @return string
 */
function hammock_get_account_page_links( $endpoint = 'base', $value = '' ) {
	$base_url = hammock_get_page_permalink( 'account_page' );
	if ( $endpoint == 'base' || $endpoint == 'dashboard' ) {
		return $base_url;
	} else {
		return hammock_get_page_endpoint_url( $base_url, $endpoint, $value );
	}
}

/**
 * Get the endpoint url
 * 
 * @param string $path - the main parent path
 * @param string $endpoint - the endpoint
 * @param string $value - optional value
 * 
 * @since 1.0.0
 * 
 * @return string
 */
function hammock_get_page_endpoint_url( $path, $endpoint, $value = '' ) {
	$query_vars = hammock()->get_query()->get_query_vars();
	$endpoint   = ! empty( $query_vars[ $endpoint ] ) ? $query_vars[ $endpoint ] : $endpoint;

	if ( get_option( 'permalink_structure' ) ) {
		if ( strstr( $path, '?' ) ) {
			$query_string 	= '?' . wp_parse_url( $path, PHP_URL_QUERY );
			$path    		= current( explode( '?', $path ) );
		} else {
			$query_string = '';
		}
		$url = trailingslashit( $path );

		if ( $value ) {
			$url .= trailingslashit( $endpoint ) . user_trailingslashit( $value );
		} else {
			$url .= user_trailingslashit( $endpoint );
		}

		$url .= $query_string;
	} else {
		$url = add_query_arg( $endpoint, $value, $path );
	}

	return apply_filters( 'hammock_get_page_endpoint_url', $url, $endpoint, $path, $value );
}

/**
 * Get Account URL
 * 
 * @since 1.0.0
 * 
 * @return string
 */
function hammock_get_page_permalink( $page_key ) {
	$page_id 	= hammock_page_id( $page_key );
	$page_url 	= get_permalink( $page_id );
	return apply_filters( 'hammock_get_page_permalink', $page_url, $page_id );
}
?>