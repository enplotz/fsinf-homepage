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

/* Widget  */

function stammtisch_booking_form()
{
  setlocale(LC_TIME, 'de_DE');
  /* Load current participants and settings from DB */

  /* Display settings and curr participants */
  ob_start();
?>
<dl>
  <dt>Ort:</dt>
  <dd><a href="<?= get_option('stammtisch_url', '#') ?>"><?= get_option('stammtisch_location', 'unbekannt') ?></a></dd>
  <dt>
    Zeit:
  </dt>
  <dd>
    <time datetime="<?= strftime('%d.%m.%Y %R', get_next_stammtisch_timestamp())?>">
      <?= strftime('%A, %R', get_next_stammtisch_timestamp())?>
    </time>
  </dd>
  <dt>Teilnehmer: <?= get_number_of_participants() ?></dt>
</dl>
<?php
  $req_number = get_option('stammtisch_required', STAMMTISCH_DEFAULT_REQUIRED);
  $participants = get_number_of_participants();
  if ($req_number > $participants) {
    ?>
    <p>Es <b>fehlen</b> noch <?= $req_number - $participants ?> Teilnehmer, damit der Stammtisch stattfinden kann.</p>
    <?php
  } else {
    ?>
      Der Stammtisch findet statt.
    <?php
  }

  /* Logged In */
  if ( is_user_logged_in() ){
    if ( user_participates() ){
      /* Link to remove me */
?>
      <a href="#">Ich komme doch nicht</a>
<?php
    } else {
?>
<ul>
  <li><a href="#">Ich komme</a></li>
  <li><a href="#">Ich komme später</a></li>
</ul>
<?php
    }
  /* Not Logged In */
  } else {
?>
<p>Bitte <a href="<?= wp_login_url() ?>">einloggen</a> oder <a href="<?= site_url('/wp-login.php?action=register') ?>">registrieren</a>, um sich für den Stammtisch anmelden zu können.</p>
<?php
  }
  $result = ob_get_contents();
  ob_end_clean();
  return $result;
}

add_shortcode('stammtisch_tool', 'stammtisch_booking_form');

function get_next_stammtisch_timestamp()
{
  $day = get_option('stammtisch_day', STAMMTISCH_DEFAULT_DAY);
  $daytime = get_option('stammtisch_time', STAMMTISCH_DEFAULT_TIME);

  $today = (int) date('w');
  $diff = ($day - $today + 7) % 7;
  $diff_seconds = $diff * 60 * 60 * 24;

  $timestamp_today = time();
  $date_today = $timestamp_today + $diff_seconds;
  return strtotime(date('Y-m-d ', $date_today) . $daytime);
}

function get_number_of_participants()
{
  global $wpdb;
  $number = $wpdb->get_var(
    $wpdb->prepare(
    "
    SELECT  COUNT(user_id)
    FROM  wp_stammtisch
    WHERE  date = %s
    ", strftime('%Y-%m-%d', get_next_stammtisch_timestamp())
    ));
  return $number;
}

function user_participates()
{
  global $wpdb;
  $participates = $wpdb->get_var(
    $wpdb->prepare(
    "
    SELECT  COUNT(user_id)
    FROM  wp_stammtisch
    WHERE  date = %s
                AND
                user_id = %d
    ", strftime('%Y-%m-%d', get_next_stammtisch_timestamp()), get_current_user_id()
    ));

  return $participates;
}
