<?php
/**
 * Utilities
 *
 * @link       http://luisabarca.com
 * @since      1.0.0
 *
 * @package    Bplus_Escuelas
 * @subpackage Bplus_Escuelas/modules
 */

/**
 * Utilities functions.
 *
 * @since      1.0.0
 * @package    Bplus_Escuelas
 * @subpackage Bplus_Escuelas/modules
 * @author     Luis Abarca <luis@bplus.mx>
 */
class Bplus_Framework_Utils {
	/**
	 * Register a new custom post type.
	 *
	 * @param array $args Options.
	 * @param array $labels Singular and plural names.
	 */
	public static function register_custom_types( $args, $labels ) {
		self::register_custom_type( $args, $labels );
	}

	/**
	 * Add role capabilities for a post type.
	 *
	 * @param string $role Role name to add.
	 * @param string $post_type Post type to add capabilities.
	 */
	public static function add_capabilities_for_post_type( $role, $post_type ) {
		$role = get_role( 'administrator' );

		// Post type capabilities.
		$custom_perms = self::get_capabilities_for_post_type( $post_type );

		foreach ( $custom_perms as $wpperm => $perm ) {
			$role->add_cap( $perm );
		}
	}

	/**
	 * List of available capabilities for a post type.
	 *
	 * @param string $post_type Post type to add capabilities.
	 * @param string $post_type_plural Post type plural name.
	 */
	public static function get_capabilities_for_post_type( $post_type, $post_type_plural = '') {

		$cap_singular = $post_type;
		$cap_plural = $post_type_plural;

		if ( empty( $cap_plural ) ) {
			$cap_plural = $cap_singular . 's';
		}

		return array(
			// Primitives.
			'edit_post'              => 'edit_' . $cap_singular,
			'read_post'              => 'read_' . $cap_singular,
			'delete_post'            => 'delete_' . $cap_singular,
			// Checked by WP Core.
			'edit_posts'             => 'edit_' . $cap_plural,
			'edit_others_posts'      => 'edit_others_' . $cap_plural,
			'publish_posts'          => 'publish_' . $cap_plural,
			'read_private_posts'     => 'read_private_' . $cap_plural,
			// Additional capabilities.
			'read'					 => 'read',
			'delete_posts'           => 'delete_' . $cap_plural,
			'delete_private_posts'   => 'delete_private_' . $cap_plural,
			'delete_published_posts' => 'delete_published_' . $cap_plural,
			'delete_others_posts'    => 'delete_others_' . $cap_plural,
			'edit_private_posts'     => 'edit_private_' . $cap_plural,
			'edit_published_posts'   => 'edit_published_' . $cap_plural,
		);
	}

	/**
	 * Generate a new custom post type
	 *
	 * @param array  $args {
	 *   Arguments for Custom post type.
	 *
	 *   @type string $slug  Slug for CPT. Ex.: bp-events.
	 * 	 @type string $singular  Singular name for custom post type. Em.: Event.
	 *   @type string $plural  Plural name for CPT. Ex.: Events.
	 *   @type string $menu_icon  Icon for menu, defaults to: dashicons-groups.
	 * }
	 * @param array  $labels {.
	 *   @type string $name  Label name;
	 * }
	 * @param string $text_domain Domain for translation.
	 */
	public static function register_custom_type( $args, $labels = array(), $text_domain = '' ) {
		$defaults = array(
			'slug'      => '',
			'singular'  => '',
			'plural'    => '',
			'menu_icon' => 'dashicons-groups',
		);

		$args = wp_parse_args( $args, $defaults );

		$slug = $args['slug'];

		$menu_icon = apply_filters( 'bp-crm-cpt-icon', $args['menu_icon'], $slug );

		$singular = $args['singular'];

		if ( empty( $args['plural'] ) ) {
			$args['plural'] = $singular . 's';
		}

		$plural = $args['plural'];

		$single_lower = strtolower( $singular );
		$plural_lower = strtolower( $plural );

		$default_labels = array(
			'name'               => __( $plural, $text_domain ),
			'singular_name'      => __( $singular, $text_domain ),
			'menu_name'          => __( $plural, $text_domain ),
		);

		$labels = wp_parse_args( $labels, $default_labels );

		$cap_singular = $slug;
        $cap_plural = $cap_singular . 's';

		$default_options = array(
			'description'     		=> '',
			'public'          		=> true,
			'show_ui'         		=> true,
			'show_in_menu'    		=> true,
			'menu_icon'				=> $menu_icon,
			'menu_position'         => 10,
			'exclude_from_search' 	=> false,
			'map_meta_cap'          => true,
			'capability_type'       => array( $cap_singular, $cap_plural ),  // directorio, directorios
            'capabilities'          => self::get_capabilities_for_post_type( $cap_singular, $cap_plural ),
			'hierarchical'    		=> false,
			'rewrite'         		=> array(
				'slug' => $slug,
			),
			'has_archive'     		=> false,
			'supports'        		=> array(
				'title',
				'excerpt',
				'editor',
				'thumbnail',
				'revisions',
				'custom-fields',
			),
			'labels'          		=> $labels,
		);

		$args = wp_parse_args( $args, $default_options );
		$args = apply_filters( 'bp-crm-cpt-options', $args, $slug );

		register_post_type( $slug, $args );
	}

	/**
	 * Register a new custom taxonomy.
	 *
	 * @param array  $args {
	 *   Arguments for Custom Taxonomy.
	 *
	 *   @type string $slug             Slug for CPT. Ex.: bp-events.
	 * 	 @type string $singular         Singular name for custom post type. Em.: Event.
	 *   @type string $plural           Plural name for CPT. Ex.: Events.
	 *   @type array|string $objects    Post types to associate the taxonomy.
	 * }
	 * @param string $options           Options for taxonomy.
	 * @param string $labels            Names for singular and plural.
	 */
	public static function register_custom_taxonomy( $args, $options = '', $labels = '' ) {
		$defaults = array(
			'slug'      => '',
			'objects'   => '',
			'singular'  => '',
			'plural'    => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$slug = $args['slug'];

		// Pasamos objetos releacionados por el filtro.
		$objects = apply_filters( 'bp_taxonomy_objects', $args['objects'], $slug );

		$singular = $args['singular'];

		if ( empty( $args['plural'] ) ) {
			$args['plural'] = $singular . 's';
		}

		$plural = $args['plural'];

		$single_lower = strtolower( $singular );
		$plural_lower = strtolower( $plural );

		/*
		 * Labels
		 *
		 */
		$default_labels = array(
			'name'              => __( $plural, 'bp-crm' ),
			'singular_name'     => __( $singular, 'bp-crm' ),
		);

		$labels = wp_parse_args( $labels, $default_labels );

		/*
		 * Options
		 *
		 */
		$default_options = array(
			'description'     		=> '',
			'public'          		=> true,
			'show_ui'         		=> true,
			'show_in_menu'    		=> true,
			'show_in_nav_menu'    	=> true,
			'show_tag_cloud'        => true,
			'hierarchical'    		=> false,
			'rewrite'         		=> array(
				'slug' => $slug,
			),
			'has_archive'     		=> false,
			'labels'          		=> $labels,
		);

		$options = wp_parse_args( $options, $default_options );
		$options = apply_filters( 'bp_taxonomy_args', $options, $slug );

		register_taxonomy( $slug, $objects, $options );
	}

	/**
	 * 
	 */
	public static function add_role( $role, $display_name, $capabilities ) {
		add_role( $role, $display_name, $capabilities );
	}

}
