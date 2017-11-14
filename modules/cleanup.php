<?php
/**
 * Custom code for WordPress
 *
 * @link       http://bplus.mx
 * @since      1.0.0
 *
 * @package    Bplus_Framework
 * @subpackage Bplus_Framework/modules
 */

/**
 * Inspired by Simon Bradburys cleanup.php fromb4st theme https://github.com/SimonPadbury/b4st
 *
 * @package understrap
 */

/**
 * Removes the generator tag with WP version numbers. Hackers will use this to find weak and old WP installs
 *
 * @return string
 */
function no_generator() {
	return '';
}

add_filter( 'the_generator', 'no_generator' );

/*
Clean up wp_head() from unused or unsecure stuff
*/
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

// Remove alkivia 0.8 headers.
remove_action( 'wp_head', '_ak_framework_meta_tags' );
remove_action( 'wp_head', 'shrsb_add_ogtags_head' );

/**
 * Show less info to users on failed login for security.
 * (Will not let a valid username be known.)
 *
 * @return string
 */
function show_less_login_info() {
	return '<strong>ERROR</strong>: ' . __( 'Stop guessing!', 'bplus-framework' );
}

add_filter( 'login_errors', 'show_less_login_info' );
