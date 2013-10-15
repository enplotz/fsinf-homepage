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
    add_action('admin_menu','fsinf_events_add_pages');
    add_action('plugins_loaded','plugins_loaded');
    // Run install script on plugin activation
    register_activation_hook(__FILE__,'fsinf_events_install');

function plugins_loaded() {
      global $pagenow;
      define('PARTICIPANTS_EXPORT_FILENAME', 'participants');
      if ($pagenow=='admin.php' &&
          current_user_can('manage_event') &&
          isset($_GET['download'])  &&
          $_GET['download']== PARTICIPANTS_EXPORT_FILENAME . '.tsv') {
        export_participants();
        exit();
      }
    }

function export_participants(){
    header("Content-type: application/x-msdownload");
    header("Content-Disposition: attachment; filename=".PARTICIPANTS_EXPORT_FILENAME.".tsv");
    header("Pragma: no-cache");
    header("Expires: 0");
    $current_event = fsinf_get_current_event();
    $registrations = fsinf_get_registrations();
    $participants = array_filter($registrations, 'is_admitted');
    # Header
    echo "NAME\tMAIL\tPHONE\tSEMESTER\tCAR\t" . ($current_event->camping ? "TENT\t" : "") . "PAID\tNOTES";
    echo "\n";
    # Data
    foreach ($participants as $participant) {
        echo $participant->first_name .' '.$participant->last_name . "\t";
        echo $participant->mail_address . "\t";
        echo $participant->mobile_phone . "\t";
        echo ($participant->semester <= 6 ? $participant->semester.'.' : 'Höheres') . "Sem " .  ($participant->bachelor == 1 ? 'Bachelor' : 'Master') . "\t";
        if ($participant->has_car == 1) :
          echo "Ein Auto mit " . $participant->car_seats . ' ' .  (($participant->car_seats) == 1 ? 'Sitz' : 'Sitzen') . "\t";
        else:
          echo "Kein Auto" . "\t";
        endif;
        if ($current_event->camping):
            if ($participant->has_tent == 1) :
                echo "Ein Zelt mit " . $participant->tent_size . ' ' . (($participant->tent_size) == 1 ? 'Schlafplatz' : 'Schlafplätzen') . "\t";
            else:
                echo "Kein Zelt" . "\t";
            endif;
        endif;
        echo $participant->paid == 1 ? 'Yep' : 'Nope' . "\t";
        echo $participant->notes . "\t";
        echo "\n";
    }
}

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
