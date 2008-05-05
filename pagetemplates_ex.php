<?php
/*
Plugin Name: Page Templates Extended
Plugin URI: http://wp-plugins.dk/pte
Description: Makes it possible to create templates for a specific page by its ID - like categories. If the page hasn't a template assigned and its a subpage, the parent page template will be used if it exists.
Author: Thomas Blomberg Hansen
Version: 1.1
Author URI: http://wp-plugins.dk/
*/

/*  
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//-----------------\\
//-- EDIT NOTICE --\\
//-----------------\\
// To set the default value of $pte_use_parent_template look for:
// "add_option('pte_use_parent_template',1);"
// If don't you wish to use the parent templates change it to:
// "add_option('pte_use_parent_template',0);"
//-----------------\\

function page_templates_ex () {

	global $wpdb,$posts,$post;

	if ( defined('WP_USE_THEMES') && constant('WP_USE_THEMES') ) { //WP is doing this check, so better be safe :D
		if ( is_page() ) {
		// This is a page
			$page_templates_ex_path = TEMPLATEPATH . '/page-' . $post->ID . '.php';
			if ( file_exists($page_templates_ex_path) ) {
			// There is created a specific template file
				include($page_templates_ex_path);
			} else {
			// There isn't created a specific template file (The template file cannot be found in this theme)
				$pte_use_parent_template = get_option('pte_use_parent_template'); //Checks to see if the use of parent templates are set.
				if ( $pte_use_parent_template == 1 ) {
				//Parent templates are set
					$page_templates_ex_parent_id = $wpdb->get_var("SELECT post_parent FROM $wpdb->posts WHERE ID = $post->ID");
					if ( $page_templates_ex_parent_id >= 1 ) {
					// This is a subpage
						$page_templates_ex_parent_has_template = false;
						do {
							$page_templates_ex_path = TEMPLATEPATH . '/page-' . $page_templates_ex_parent_id . '.php';
							if ( file_exists($page_templates_ex_path) ) {
							//There is created a specific template file for the parent
								include($page_templates_ex_path);
								$page_templates_ex_parent_has_template = true;
							} else {
								$page_templates_ex_parent_id = $wpdb->get_var("SELECT post_parent FROM $wpdb->posts WHERE ID = ".$page_templates_ex_parent_id);
								if ( $page_templates_ex_parent_id != 0 ) {
									$page_templates_ex_path = TEMPLATEPATH . '/page-' . $page_templates_ex_parent_id . '.php';
									if ( file_exists($page_templates_ex_path) ) {
									//There is created a specific template file for the parent
										include($page_templates_ex_path);
										$page_templates_ex_parent_has_template = true;
									} else {
										$page_templates_ex_parent_has_template = false;
									}
								} else {
									// Has no parents, break the loop.
									break;
								}
							}
						} while ($page_templates_ex_parent_has_template == false);
					} else {
					// This isn't a subpage and has no assigned template file
						// Uses the default WP page template
						return;
					}
				} else {
				//Parent templates are NOT set
					// Uses the default WP page template
					return;
				}
			}
			exit;
		}
	}
}
				

function set_page_templates_ex_options () {
	add_option('pte_use_parent_template',1);
}

function unset_page_templates_ex_options () {
	delete_option('pte_use_parent_template');
}

function optionspage_page_templates_ex () {
?>
<div class="wrap">
    <?php 
	if ( $_REQUEST['submit'] ) {
		update_page_templates_ex_options ();
	}
	?>
	<h2><?php _e('Page Templates Extended Settings','pagetemplates_ex'); ?></h2>
    <form method="post" action="options.php">
		<?php
        wp_nonce_field('update-options');
            
        if ( get_option('pte_use_parent_template') == 1 ) {
        	$page_templates_ex_input_yes_selected = 'checked';
            $page_templates_ex_input_no_selected = '';
		} else {
            $page_templates_ex_input_yes_selected = '';
            $page_templates_ex_input_no_selected = 'checked';
        }
        ?>
    
        <h3><?php _e('Use the template of the parent page?','pagetemplates_ex'); ?></h3>
        <p><?php _e('- If it exists and the subpage hasn\'t a template set.','pagetemplates_ex'); ?></p>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="page_templates_ex_input_yes"><?php _e('Yes'); ?></label></th>
                <td><input type="radio" value="1" name="pte_use_parent_template" id="page_templates_ex_input_yes" <?php echo $page_templates_ex_input_yes_selected; ?> tabindex="1" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="page_templates_ex_input_no"><?php _e('No'); ?></label></th>
                <td><input type="radio" value="0" name="pte_use_parent_template" id="page_templates_ex_input_no" <?php echo $page_templates_ex_input_no_selected; ?> tabindex="2" /> <em>(<?php _e('Maybe, I should use the "Page Template" option instead ;)','pagetemplates_ex'); ?>)</em></td>
            </tr>
        </table>
        <p class="submit">
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="pte_use_parent_template" />
            <input type="submit" name="Submit" value="<?php _e('Save Changes'); ?>" tabindex="3" />
        </p>
    
    </form>
</div>
<?php
}

function update_page_templates_ex_options () {
	if ( $_REQUEST['pte_use_parent_template'] ) {
		update_option('pte_use_parent_template',$_REQUEST['pte_use_parent_template']);
		?>
        <div id="message" class="updated fade">
        	<p><?php _e('Options saved.'); ?></p>
		</div>
        <?php
	} else {
		?>
        <div id="message" class="error fade">
        	<p><?php _e('Failed to save options.'); ?></p>
		</div>
        <?php
	}
}

function modify_menu_page_templates_ex () {
	add_options_page(
		__('Page Templates Extended Settings','pagetemplates_ex'), //Page title
		__('PTE','pagetemplates_ex'), //Menu title
		'manage_options', //Access_level/capability
		__FILE__, //File
		'optionspage_page_templates_ex' //Function
	);
}

load_plugin_textdomain('pagetemplates_ex', 'wp-content/plugins/'.plugin_basename(dirname(__FILE__))); //Enables gettext translating

add_action('admin_menu','modify_menu_page_templates_ex'); //Adds the options page in the administration
add_action('template_redirect','page_templates_ex'); //Adds the function to the template_redirect Hook
register_activation_hook(__FILE__,'set_page_templates_ex_options'); //Run the function when the plugin is activated
register_deactivation_hook(__FILE__,'unset_page_templates_ex_options'); //Run the function when the plugin is deactivated
?>