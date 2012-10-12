<?php

// Hook for adding admin menus
add_action('admin_menu', 'fsinf_events_add_pages');

// Run install script on plugin activation
register_activation_hook(__FILE__,'fsinf_events_install');

// Add shortcode for latest event
add_shortcode('fsinf_current_event_booking', 'fsfin_events_booking_form');
add_shortcode('fsinf_current_event_details', 'fsfin_events_details');

// Add JS to Admin head
# add_action('admin_head', 'fsinf_events_js');

// Send HTML Emails
#add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
