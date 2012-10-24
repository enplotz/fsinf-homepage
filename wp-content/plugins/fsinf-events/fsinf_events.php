<?php
/*
Plugin Name: FSInf-Events
Plugin URI: http://fachschaft.inf.uni-konstanz.de
Description: A tool for creating simple events. It includes a listing of participants.
Version: 0.1.0
Author: Fachschaft Informatik Uni Konstanz
Author URI: http://fachschaft.inf.uni-konstanz.de
License: A license will be determined in the near future.
*/

// Constants
global $wpdb;

define('FSINF_EVENTS_TABLE', $wpdb->prefix . "fsinf_events");
define('FSINF_PARTICIPANTS_TABLE', $wpdb->prefix . "fsinf_participants");

// Hook for adding admin menus
add_action('admin_menu', 'fsinf_events_add_pages');

// Run install script on plugin activation
register_activation_hook(__FILE__,'fsinf_events_install');

/* Database */
/* install relevant database tables */
include 'fsinf_database.php';

/* Little Helper Functions */
include 'fsinf_events_helpers.php';

// Add Pages to Admin Menu
function fsinf_events_add_pages() {
    // Add a new top-level menu (ill-advised):
    add_menu_page(__('FSInf-Events','fsinf-events'), __('FSInf-Events','fsinf-events'), 'manage_options', 'fsinf-events-top-level-handle', 'fsinf_events_toplevel_page' );
    // Add a submenu to the custom top-level menu:
    add_submenu_page('fsinf-events-top-level-handle', __('Neues Event','fsinf-events-new'), __('Neues Event','fsinf-events-new'), 'manage_options', 'fsinf-add-event-page', 'fsinf_add_event_page');
    // Add a second submenu to the custom top-level menu:
    add_submenu_page('fsinf-events-top-level-handle', __('Alle Events','fsinf-events-all'), __('Alle Events','fsinf-events-all'), 'manage_options', 'fsinf-all-events-page', 'fsinf_all_events_page');
}

// mt_toplevel_page() displays the page content for the custom FSInf-Events menu
include 'pages/toplevel_page.php';

// mt_sublevel_page() displays the page content for the first submenu
// of the custom FSInf-Events menu
include 'pages/new_event_page.php';

// mt_sublevel_page2() displays the page content for the second submenu
// of the custom FSInf-Events menu
include 'pages/all_events_page.php';

// Models for Events and Participants
include 'fsinf_events_models.php';

// Validators for Models
include 'fsinf_events_validators.php';

// Validate Registration, Save to database, send mail and show message
include 'fsinf_events_registration_controller.php';

include 'fsinf_events_new_controller.php';

/*
    FS Inf Events booking form.
    Embed with shortcode: [fsinf_current_event_booking]
 */
include 'pages/registration_page.php';

// Show details page for current event
include 'pages/details_page.php';

