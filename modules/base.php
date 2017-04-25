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

// Remueve la etiqueta del framework alkivia 0.8 !
remove_action( 'wp_head', '_ak_framework_meta_tags' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'shrsb_add_ogtags_head' );
