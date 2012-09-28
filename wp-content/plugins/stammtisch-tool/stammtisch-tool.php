<?php /*
Plugin Name: Stammtisch-Tool
Plugin URI: http://fachschaft.inf.uni-konstanz.de/
Description: Allows for easy booking of a regulars table.
Author: Manuel Hotz
Version: 0.1
Author URI: http://enplotz.de
*/


/*
 *  DATABASE STUFF
 */
define ('STAMMTISCH_DEFAULT_REQUIRED', 3);
define ('STAMMTISCH_DEFAULT_TIME', '20:00:00');
/* Sunday 0, Saturday 6 */
define ('STAMMTISCH_DEFAULT_DAY', 3);
define ('STAMMTISCH_DEFAULT_LOCATION', 'Defne');
define ('STAMMTISCH_DEFAULT_URL', 'http://defnekn.de');


$stammtisch_options =
  array(
    "stammtisch_required" => STAMMTISCH_DEFAULT_REQUIRED,
    "stammtisch_time"     => STAMMTISCH_DEFAULT_TIME,
    "stammtisch_day"      => STAMMTISCH_DEFAULT_DAY,
    "stammtisch_location" => STAMMTISCH_DEFAULT_LOCATION,
    "stammtisch_url"      => STAMMTISCH_DEFAULT_URL
  );


function stammtisch_install (){
  global $wpdb, $stammtisch_options;

  $table_name = $wpdb->prefix . "stammtisch";

  $sql = "CREATE TABLE  $table_name (
            user_id BIGINT( 20 ) UNSIGNED NOT NULL,
            date DATE NOT NULL,
            arrives_later TINYINT( 1 ) NOT NULL,
            PRIMARY KEY  (  user_id ,  date )
          ) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_unicode_ci;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);

  foreach ($stammtisch_options as $name => $default) {
    add_option($name, $default);
  }
}

register_activation_hook(__FILE__,'stammtisch_install');
/**********************************************************************/

/*
 * ADMIN MENU
 */

add_action('admin_menu', 'stammtisch_add_admin_page');

function stammtisch_add_admin_page()
{
  add_menu_page(__('Stammtisch-Tool', 'stammtisch-tool'), __('Stammtisch-Tool', 'stammtisch-tool'), 'manage_options', 'stammtisch-tool', 'stammtisch_admin_page');
}

function stammtisch_admin_page()
{
  ?>
    <div class="wrap">
        <div class="icon32" id="icon-options-general"></div>
        <h2>Stammtisch Tool Administration</h2>
        <form action="options.php" method="post">
            <p class="submit">
                <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes','wptuts_textdomain'); ?>" />
            </p>
        </form>
    </div><!-- wrap -->
<?php
}

function stammtisch_register_settings()
{
  global $stammtisch_options;

  foreach ($stammtisch_options as $key => $value) {
    register_setting('stammtisch_settings', $key, 'stammtisch_validate_options');
  }
}

function stammtisch_validate_options($input)
{
  /* Do nothing until we have time for validation :P */
  return $input;
}





