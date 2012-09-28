<?php /*
Plugin Name: Stammtisch-Tool
Plugin URI: http://fachschaft.inf.uni-konstanz.de/
Description: Allows for easy booking of a regulars table.
Author: Manuel Hotz
Version: 0.1
Author URI: http://enplotz.de
*/

function stammtisch_install (){
  global $wpdb;

  $table_name = $wpdb->prefix . "stammtisch";

  $sql = "CREATE TABLE  $table_name (
            user_id BIGINT( 20 ) UNSIGNED NOT NULL,
            date DATE NOT NULL,
            arrives_later TINYINT( 1 ) NOT NULL,
            PRIMARY KEY  (  user_id ,  date )
          ) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_unicode_ci;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}


register_activation_hook(__FILE__,'stammtisch_install');