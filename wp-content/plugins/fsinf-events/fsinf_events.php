<?php
/*
Plugin Name: FSInf-Events
Plugin URI: http://fachschaft.inf.uni-konstanz.de
Description: A tool for creating simple events, even recurring ones like a regulars table. It includes a listing of participants.
Version: 0.1.0
Author: Fachschaft Informatik Uni Konstanz
Author URI: http://fachschaft.inf.uni-konstanz.de
License: A license will be determined in the near future.
*/


// Hook for adding admin menus
add_action('admin_menu', 'fe_add_pages');

// action function for above hook
function fe_add_pages() {

    // Add a new top-level menu (ill-advised):
    add_menu_page(__('FSInf-Events','fsinf-events'), __('FSInf-Events','fsinf-events'), 'manage_options', 'mt-top-level-handle', 'mt_toplevel_page' );

    // Add a submenu to the custom top-level menu:
    add_submenu_page('mt-top-level-handle', __('New Event','fsinf-events'), __('New Event','fsinf-events'), 'manage_options', 'sub-page', 'mt_sublevel_page');

    // Add a second submenu to the custom top-level menu:
    add_submenu_page('mt-top-level-handle', __('Test Sublevel 2','fsinf-events'), __('Test Sublevel 2','fsinf-events'), 'manage_options', 'sub-page2', 'mt_sublevel_page2');
}

// mt_toplevel_page() displays the page content for the custom FSInf-Events menu
function mt_toplevel_page() {
    echo "<h2>" . __( 'FSInf-Events', 'fsinf-events' ) . "</h2>";
}

// mt_sublevel_page() displays the page content for the first submenu
// of the custom FSInf-Events menu
function mt_sublevel_page() {
    echo "<h2>" . __( 'Create a new Event', 'fsinf-events' ) . "</h2>";
}

// mt_sublevel_page2() displays the page content for the second submenu
// of the custom FSInf-Events menu
function mt_sublevel_page2() {
    echo "<h2>" . __( 'Test Sublevel2', 'fsinf-events' ) . "</h2>";
}


?>