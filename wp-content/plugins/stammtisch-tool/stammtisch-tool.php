<?php /*
Plugin Name: Stammtisch-Tool
Plugin URI: http://fachschaft.inf.uni-konstanz.de/
Description: Allows for easy booking of a regulars table.
Author: Manuel Hotz, Florian Junghanns, Leonard Wörteler
Version: 0.1
Author URI: http://fachschaft.inf.uni-konstanz.de/
License: A license will be determined in the near future.
*/


/*
 *  DATABASE STUFF
 */
define ('STAMMTISCH_DEFAULT_REQUIRED', 3);
define ('STAMMTISCH_DEFAULT_TIME', '20:00:00');
/* Sunday 0, Saturday 6 */
define ('STAMMTISCH_DEFAULT_DAY', 3);
define ('STAMMTISCH_DEFAULT_LOCATION', 'Defne');
define ('STAMMTISCH_DEFAULT_URL', 'http://defne-kn.de');

# TODO: make options
define ('STAMMTISCH_DEFAULT_LOCK_HOURS', 3);
# Responsible person must be user_login
define ('STAMMTISCH_DEFAULT_RESPONSIBLE_PERSON', 'max');


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
if (is_admin()){
add_action('admin_menu', 'stammtisch_add_admin_page');

function stammtisch_add_admin_page()
{
  add_menu_page(__('Stammtisch-Tool', 'stammtisch-tool'), __('Stammtisch-Tool', 'stammtisch-tool'), 'manage_options', 'stammtisch-tool', 'stammtisch_admin_page');
}

function is_selected_day($day_int)
{
  return (get_option('stammtisch_day', STAMMTISCH_DEFAULT_DAY) == $day_int) ? 'selected' : '';
}


function stammtisch_admin_page()
{

  /* Process cancelation of admin for specific user */
  if (is_admin()){
    if ( array_key_exists('participation_cancel_for', $_POST)){
        cancel_participation_for($_POST['participation_cancel_for']);
    }
  }

    global $stammtisch_options;

    foreach ($stammtisch_options as $key => $value) {
      if ( array_key_exists($key, $_POST)){
        if($_POST[$key] != ''){
          update_option($key, $_POST[$key]);
        }
      }
    }
  ?>
    <div class="wrap">
        <div class="icon32" id="icon-options-general"></div>
        <h2>Stammtisch-Tool Administration</h2>
        <form action="" method="post" class="form-horizontal">
          <div class="control-group">
            <label class="control-label" for="stammtischDay">Tag</label>
            <div class="controls">
              <select name="stammtisch_day" id="stammtischDay">
                <option name="stammtisch_day" <?= is_selected_day(1) ?> value="1">Montag</option>
                <option name="stammtisch_day" <?= is_selected_day(2) ?> value="2">Dienstag</option>
                <option name="stammtisch_day" <?= is_selected_day(3) ?> value="3">Mittwoch</option>
                <option name="stammtisch_day" <?= is_selected_day(4) ?> value="4">Donnerstag</option>
                <option name="stammtisch_day" <?= is_selected_day(5) ?> value="5">Freitag</option>
                <option name="stammtisch_day" <?= is_selected_day(6) ?> value="6">Samstag</option>
                <option name="stammtisch_day" <?= is_selected_day(7) ?> value="7">Sonntag</option>
            </select>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="stammtischTime">Zeit</label>
            <div class="controls">
              <input type="text" name="stammtisch_time" id="stammtischTime" value="<?= get_option('stammtisch_time', STAMMTISCH_DEFAULT_TIME);?>" />
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="stammtischLocation">Ort</label>
            <div class="controls">
              <input type="text" name="stammtisch_location" id="stammtischLocation"  value="<?= get_option('stammtisch_location', STAMMTISCH_DEFAULT_LOCATION);?>" />
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="stammtischUrl">URL des Ortes</label>
            <div class="controls">
              <input type="url" name="stammtisch_url" id="stammtischUrl"  value="<?= get_option('stammtisch_url', STAMMTISCH_DEFAULT_URL);?>"/>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="stammtischRequired">Mindestanzahl an Teilnehmern</label>
            <div class="controls">
              <input type="number" name="stammtisch_required" id="stammtischRequired"  value="<?= get_option('stammtisch_required', STAMMTISCH_DEFAULT_REQUIRED);?>" />
            </div>
          </div>
          <div class="control-group">
            <div class="controls">
              <button type="submit" class="btn"><?php esc_attr_e('Save Changes','stammtisch_tool_save'); ?></button>
            </div>
          </div>
        </form>
        <form action="" method="post">
          <div class="span6">
            <h4>Teilnehmer des aktuellen Stammtisches:</h4>
            <!--<pre>
              <?= print_r(get_participants()) ?>
            </pre>-->
<?php
            $participants = get_participants();
            if (count($participants) > 0) {
?>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>
                    Teilnehmer
                  </th>
                  <th>
                    Erscheint
                  </th>
                  <th>
                    Teilnahme bearbeiten
                  </th>
                </tr>
              </thead>
              <tbody>
<?php
            for ($i=0; $i < count($participants); $i++) {
?>
                <tr>
                  <td>
                    <?= $participants[$i]->display_name; ?>
                  </td>
                  <td>
<?php
                    if ($participants[$i]->arrives_later){
                    echo '<i class="icon-time"></i> später';
                    } else {
                    echo '<i class="icon-ok"></i> regulär';
                    }
?>
                  </td>
                  <td>
                    <form action="" method="post">
                      <input type="hidden" value="<?= $participants[$i]->user_id ?>" name="participation_cancel_for"/>
                      <button type="submit" class="btn btn-danger btn-small"><i class="icon-remove icon-white"></i> Nimmt doch nicht teil</button>
                    </form>
                  </td>
                </tr>
<?php
            }
            } else {
?>
              <p>Der aktuelle Stammtisch hat noch keine Teilnehmer.</p>
<?php
            }
?>
              </tbody>
            </table>
          </div>
    </div><!-- wrap -->
<?php
}

function stammtisch_register_settings()
{
  global $stammtisch_options;

  foreach ($stammtisch_options as $key => $value) {
    register_setting('stammtisch_settings', $key, 'stammtisch_validate_options');
    add_settings_section($key, $key, 'main_section_text', 'stammtisch-tool');
    add_settings_field('stammtisch_text_string', 'Plugin Text Input', 'stammtisch_setting_string', 'stammtisch-tool', $key);
  }
}

function main_section_text() {
  echo '<p>Main description of this section here.</p>';
}

function stammtisch_setting_string(){
  echo "TEST";
}

function stammtisch_validate_options($input)
{
  /* Do nothing until we have time for validation :P */
  return $input;
}
}
/**********************************************************************/

/*
 * WIDGET
 * (als Text-Widget mit short_code [stammtisch_tool] einbinden)
 */

function stammtisch_booking_form()
{
  setlocale(LC_TIME, 'de_DE');

  /* Process POST values and prepare alerts for later display*/
  if (is_user_logged_in()){
    if ( array_key_exists('participation', $_POST)){
      if ($_POST['participation'] === 'join') {
        if ( join_regulars_table(0) ){
          $stammtisch_alert = '<div class="alert alert-success">
            <a class="close" data-dismiss="alert">×</a>
            <p>Yay! Cool, dass du zum Stammtisch kommst!</p>
          </div>';
         } else {
          $stammtisch_alert = '<div class="alert alert-error">
                        <a class="close" data-dismiss="alert">×</a>
                        <p>Die Registrierung ist leider bereits geschlossen :(</p>
                        </div>';
        }

      } elseif ($_POST['participation'] === 'join_later') {

        if (join_regulars_table(1)){
          $stammtisch_alert = '<div class="alert alert-success">
                        <a class="close" data-dismiss="alert">×</a>
                        <p>Yay! Cool, dass du zum Stammtisch kommst!</p>
                        </div>';
        } else {
          $stammtisch_alert = '<div class="alert alert-error">
                        <a class="close" data-dismiss="alert">×</a>
                        <p>Die Registrierung ist leider bereits geschlossen :(</p>
                        </div>';
        }

      } elseif ($_POST['participation'] === 'cancel') {
        cancel_participation();
        $stammtisch_alert = '<div class="alert alert-success">
          <a class="close" data-dismiss="alert">×</a>
          <p>Schade, dass du doch nicht zum Stammtisch kommst :(</p>
        </div>';
      }
      }
  }

  /* Display settings and curr participants */
  ob_start();

    $req_number = get_option('stammtisch_required', STAMMTISCH_DEFAULT_REQUIRED);
  $participants = get_number_of_participants();
  if ($req_number > $participants) {
    if (($req_number - $participants) === 1) {
?>
      <p>Es <b>fehlt</b> noch ein Teilnehmer, damit der Stammtisch stattfinden kann. Auf, auf!</p>
<?php
    } else {
?>
      <p>Es <b>fehlen</b> noch <?= $req_number - $participants ?> Teilnehmer, damit der Stammtisch stattfinden kann.</p>
<?php
    }
  } else {
?>
    <div class="alert alert-info">
      <a class="close" data-dismiss="alert">×</a>
      <p>Der Stammtisch findet statt!</p>
    </div>
<?php
  }
?>
<h4>Nächstes Treffen:</h4>
<p><time datetime="<?= strftime('%d.%m.%Y %R', get_next_stammtisch_timestamp())?>">
      <?= strftime('%A, %R', get_next_stammtisch_timestamp())?>
    </time></p>
<dl>
  <dt>Ort:</dt>
  <dd><a href="<?= get_option('stammtisch_url', '#') ?>"><?= get_option('stammtisch_location', 'unbekannt') ?></a></dd>
  <dt>Teilnehmer:</dt>
  <dd><?= get_number_of_participants() ?></dd>
</dl>
<?php

  /* Produce the alert */
  if (isset($stammtisch_alert)){
    echo $stammtisch_alert;
  }

  /* Logged In */
  if ( is_user_logged_in() ){
    if ( user_participates() ){
      /* Link to remove me */
?>
      <form action="" method="post">
        <input type="hidden" value="cancel" name="participation"/>
        <button type="submit" class="btn btn-primary btn-small"><i class="icon-remove"></i> Ich komme doch nicht</button>
      </form>
<?php
    } else {
?>

<form action="" method="post">
  <input type="hidden" value="join" name="participation"/>
  <button type="submit" class="btn btn-primary btn-small"><i class="icon-ok icon-white"></i> Ich komme</button>
</form>
<form action="" method="post">
  <input type="hidden" value="join_later" name="participation"/>
  <button type="submit" class="btn btn-small"><i class="icon-time"></i> Ich komme später</button>
</form>

<?php
    }
  /* Not Logged In */
  } else {
?>
<p>Bitte <a href="<?= wp_login_url() ?>">einloggen</a> oder <a href="<?= site_url('/wp-login.php?action=register') ?>">registrieren</a>, um sich für den Stammtisch anmelden zu können.</p>
<?php
  }
?>
<p><small>Wendet euch für Rückfragen bitte an <a href="<?= site_url('/fachschaft/mitglieder/#'.STAMMTISCH_DEFAULT_RESPONSIBLE_PERSON) ?>"><?= ucfirst(STAMMTISCH_DEFAULT_RESPONSIBLE_PERSON) ?></a>.</small></p>
<?php
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

function can_participate()
{
  if (get_next_stammtisch_timestamp() - time() < 60 * 60 * STAMMTISCH_DEFAULT_LOCK_HOURS) {
    return false;
  } else {
    return true;
  }
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

function get_participants()
{
  global $wpdb;
  $table_name = $wpdb->prefix . "stammtisch";

  $results = $wpdb->get_results(
    $wpdb->prepare(
    "
    SELECT  user_id, arrives_later, display_name
    FROM  $table_name ws
    INNER JOIN  wp_users wu ON ws.user_id = wu.id
    WHERE  date = %s
    ", strftime('%Y-%m-%d', get_next_stammtisch_timestamp())
    ));
  return $results;
}

function join_regulars_table($later){
  if (can_participate()){
  global $wpdb;
  $table_name = $wpdb->prefix . "stammtisch";
  $wpdb->insert( $table_name,
          array(
                'user_id' => get_current_user_id(),
                'date' => strftime('%Y-%m-%d', get_next_stammtisch_timestamp()),
                'arrives_later' => $later
          ),
          array(
                '%d',
                '%s',
                '%d'
          )
    );
  return true;
  }
  return false;
}

function cancel_participation_for($user_id)
{
  global $wpdb;
  $table_name = $wpdb->prefix . "stammtisch";
  if (is_admin()){
    $wpdb->query(
    $wpdb->prepare(
            "
            DELETE FROM $table_name
            WHERE  date = %s
                AND
                user_id = %d
            ", strftime('%Y-%m-%d', get_next_stammtisch_timestamp()), $user_id
    )
  );
  }
}

function cancel_participation(){
  global $wpdb;
  $table_name = $wpdb->prefix . "stammtisch";
  $wpdb->query(
    $wpdb->prepare(
            "
            DELETE FROM $table_name
            WHERE  date = %s
                AND
                user_id = %d
            ", strftime('%Y-%m-%d', get_next_stammtisch_timestamp()), get_current_user_id()
    )
  );
}
/**********************************************************************/
