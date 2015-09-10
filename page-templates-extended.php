<?php

/*
Plugin Name: Page Templates Extended
Description: Create templates for a specific page by its ID. If the page doesn't have a template assigned, the plugin looks up in the hierarchy and grabs the first template that exists.
Version: 2.0
Author: Thomas Blomberg Hansen
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: page-template-extended
*/

// Clean up previous versions database entries, we really don't need an options page and the ability to turn of the functionality, just disable/enable the plugin.
add_action( 'admin_notices', 'thomasdk81_pte_admin_notice' );
function thomasdk81_pte_admin_notice() {
	if( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
		global $pagenow;
		if( $pagenow == 'plugins.php' ){
			if ( get_option( 'pte_use_parent_template' ) ) {
				delete_option( 'pte_use_parent_template' );
				$html  = '<div class="updated"><p>';
				$html .= 'Page Template Extended is now without an options page, just enable/disable the plugin';
				$html .= '</p></div>';
				echo $html;
			}
		}
	}
}

add_action( 'template_include', 'thomasdk81_pte' );
function thomasdk81_pte() {

	if ( defined( 'WP_USE_THEMES' ) && constant( 'WP_USE_THEMES' ) && is_page() ) {
		$pte_template_found = false;

		// Start from the bottom of this pages hierarchy and look up through the parents
		do {
			// Grab page ID
			if ( ! isset( $pte_page_id ) ) {
				global $wpdb, $post;
				$pte_page_id = $post->ID;
			}

			// Locate template file
			$pte_page_template = locate_template( '/page-' . $pte_page_id . '.php' );
			if ( file_exists( $pte_page_template ) ) {
				// We found the template file - DONE
				include_once( $pte_page_template );
				$pte_template_found = true;
			} else {
				// Lets look further up the hierarchy and re-loop
				$pte_page_id = $wpdb->get_var( "SELECT post_parent FROM $wpdb->posts WHERE ID = $pte_page_id" );
				$pte_template_found = false;
			}
		} while ( $pte_template_found == false );
	}
}