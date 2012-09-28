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


// Hook for adding admin menus
add_action('admin_menu', 'fsinf_events_add_pages');

// action function for above hook
function fsinf_events_add_pages() {

    // Add a new top-level menu (ill-advised):
    add_menu_page(__('FSInf-Events','fsinf-events'), __('FSInf-Events','fsinf-events'), 'manage_options', 'fsinf-events-top-level-handle', 'fsinf_events_toplevel_page' );

    // Add a submenu to the custom top-level menu:
    add_submenu_page('fsinf-events-top-level-handle', __('New Event','fsinf-events-new'), __('New Event','fsinf-events-new'), 'manage_options', 'fsinf-add-event-page', 'fsinf_add_event_page');

    // Add a second submenu to the custom top-level menu:
    add_submenu_page('fsinf-events-top-level-handle', __('All Events','fsinf-events-all'), __('All Events','fsinf-events-all'), 'manage_options', 'fsinf-all-events-page', 'fsinf_all_events_page');
}

// mt_toplevel_page() displays the page content for the custom FSInf-Events menu
function fsinf_events_toplevel_page() {
    echo "<h2>" . __( 'FSInf-Events', 'fsinf-events' ) . "</h2>";
}

// mt_sublevel_page() displays the page content for the first submenu
// of the custom FSInf-Events menu
function fsinf_add_event_page() {
    echo "<h2>" . __( 'Create a new Event', 'fsinf-events' ) . "</h2>";
}

// mt_sublevel_page2() displays the page content for the second submenu
// of the custom FSInf-Events menu
function fsinf_all_events_page() {
    echo "<h2>" . __( 'All Events', 'fsinf-events' ) . "</h2>";
}


?>