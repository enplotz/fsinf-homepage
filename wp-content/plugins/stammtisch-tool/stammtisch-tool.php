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
define ('STAMMTISCH_DEFAULT_RESPONSIBLE_MAIL', 'fs@fachschaft.inf.uni-konstanz.de');


function stammtisch_options()
{
  return array(
    "stammtisch_required" => STAMMTISCH_DEFAULT_REQUIRED,
    "stammtisch_time"     => STAMMTISCH_DEFAULT_TIME,
    "stammtisch_day"      => STAMMTISCH_DEFAULT_DAY,
    "stammtisch_location" => STAMMTISCH_DEFAULT_LOCATION,
    "stammtisch_url"      => STAMMTISCH_DEFAULT_URL,
    "stammtisch_lock"     => STAMMTISCH_DEFAULT_LOCK_HOURS,
    "stammtisch_mail"     => STAMMTISCH_DEFAULT_RESPONSIBLE_MAIL
 );
}

function stammtisch_weekday($day_int) {
  $week = array('Sonntag', 'Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
  return $week[($day_int + 7) % 7];
}

function stammtisch_install() {
  global $wpdb;

  $table_name = $wpdb->prefix . "stammtisch";

  $sql = "CREATE TABLE  $table_name (
            user_id BIGINT(20) UNSIGNED NOT NULL,
            date DATE NOT NULL,
            arrives_later TINYINT(1) NOT NULL,
            PRIMARY KEY  ( user_id ,  date)
         ) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_unicode_ci;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);

  foreach (stammtisch_options() as $name => $default) {
    add_option($name, $default);
  }
}

# TODO: uninstall

register_activation_hook(__FILE__,'stammtisch_install');
/**********************************************************************/

/*
 * STAMMTISCH ADMIN MENU
 */
if ( current_user_can( 'manage_stammtisch' ) ) {
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

  /* Process addition or cancelation by admin for specific user */
  if (is_admin()) {

    if (array_key_exists('participation_cancel_for', $_POST)) {
      cancel_participation_for($_POST['participation_cancel_for']);
    }

    if (array_key_exists('add_participation_for', $_POST)) {
      add_participation_for($_POST['add_participation_for'], array_key_exists('joins_later', $_POST));
    }
  }

    foreach (stammtisch_options() as $key => $value) {
      if (array_key_exists($key, $_POST) && $_POST[$key] !== '') {
        update_option($key, $_POST[$key]);
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
<?php
for($i = 1; $i <= 7; $i++):
?>                <option <?= is_selected_day($i) ?> value="<?=$i?>"><?=stammtisch_weekday($i)?></option>
<?php
endfor;
?>
            </select>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="stammtischTime">Zeit</label>
            <div class="controls">
              <input type="text" name="stammtisch_time" id="stammtischTime" value="<?= get_option('stammtisch_time', STAMMTISCH_DEFAULT_TIME);?>" placeholder="z.B. 20:00:00" />
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
            <label class="control-label" for="stammtischLock">Registrierungssperre vor dem Stammtisch</label>
            <div class="controls">
              <div class="input-append">
                <input type="text" name="stammtisch_lock" id="stammtischLock"  value="<?= get_option('stammtisch_lock', STAMMTISCH_DEFAULT_LOCK_HOURS);?>" /><span class="add-on">Stunden</span>
              </div>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="stammtischMail">Mail-Adresse</label>
            <div class="controls">
              <input type="text" name="stammtisch_mail" id="stammtischMail"  value="<?= get_option('stammtisch_mail', STAMMTISCH_DEFAULT_RESPONSIBLE_MAIL);?>" />
            </div>
          </div>
          <div class="control-group">
            <div class="controls">
              <button type="submit" class="btn"><?php esc_attr_e('Save Changes','stammtisch_tool_save'); ?></button>
            </div>
          </div>
        </form>
        <div class="row">
      <div class="span6">
            <h4>Teilnehmer des aktuellen Stammtisches</h4>
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
              foreach ($participants as $participant):
?>
                  <tr>
                    <td>
                      <?= $participant->display_name; ?>
                    </td>
                    <td>
<?php
                      if ($participant->arrives_later) {
                      echo '<i class="icon-time"></i> später';
                      } else {
                      echo '<i class="icon-ok"></i> regulär';
                      }
?>
                    </td>
                    <td>
                      <form action="" method="post">
                        <input type="hidden" value="<?= $participant->user_id ?>" name="participation_cancel_for"/>
                        <button type="submit" class="btn btn-danger btn-small"><i class="icon-remove"></i> Nimmt doch nicht teil</button>
                      </form>
                    </td>
                  </tr>
<?php         endforeach;
            } else {
?>
              <p>Der aktuelle Stammtisch hat noch keine Teilnehmer.</p>
<?php
            }
?>
              </tbody>
            </table>
          </div>
          <div class="span4 well">
            <h4>Teilnehmer hinzufügen</h4>

              <form action="" method="post">
                <!-- TODO: auf user_login wechseln und prüfen obs den gibt... -->
                <label class="control-label" for="inputUser">User ID</label>
                  <div class="controls">
                    <input type="number" id="inputEmail" placeholder="1" name="add_participation_for" class="span2"/>
                  </div>

            <div class="control-group">
              <div class="controls">
                <label class="checkbox">
                  <i class="icon-time"></i>
                  <input type="checkbox" value="1" name="joins_later">
                  Erscheint später.
                </label>
                <button type="submit" class="btn"><i class="icon-ok"></i> Hinzufügen!</button>
              </div>
            </div>
              </form>
              <pre>
          <?= print_r($_POST, true) ?>
        </pre>
          </div>
        </div>
    </div><!-- wrap -->

<?php
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
  if (is_user_logged_in()) {
    if (array_key_exists('participation', $_POST)) {
      if ($_POST['participation'] === 'join') {
        if (join_regulars_table(false)) {
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

        if (join_regulars_table(true)) {
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
  if (isset($stammtisch_alert)) {
    echo $stammtisch_alert;
  }

  /* Logged In */
  if (is_user_logged_in()) {
    if (user_participates()) {
      /* Link to remove me */
?>
      <form action="" method="post" style="display: inline;">
        <input type="hidden" value="cancel" name="participation"/>
        <button type="submit" class="btn btn-primary btn-small"><i class="icon-remove icon-white"></i> Ich komme doch nicht</button>
      </form>
<?php
      if (get_participation_for_current_user()->arrives_later){
          // arrives later
          ?>
<form action="" method="post" style="display: inline;">
  <input type="hidden" value="join" name="participation"/>
  <button type="submit" class="btn btn-small"><i class="icon-ok icon-white"></i> Ich schaffe es doch rechtzeitig</button>
</form>
          <?php
      } else {
        // is on time
        ?><form action="" method="post" style="display: inline;">
  <input type="hidden" value="join_later" name="participation"/>
  <button type="submit" class="btn btn-small"><i class="icon-time"></i> Ich komme doch später</button>
</form>
        <?php
      }
    } else {
?>

<form action="" method="post" style="display: inline;">
  <input type="hidden" value="join" name="participation"/>
  <button type="submit" class="btn btn-primary btn-small"><i class="icon-ok icon-white"></i> Ich komme</button>
</form>
<form action="" method="post" style="display: inline;">
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
<p><small>Wendet euch für Rückfragen bitte an <?php get_option('stammtisch_mail', STAMMTISCH_DEFAULT_RESPONSIBLE_MAIL) ?>.</small></p>
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
  $date_today = strtotime(sprintf("+%d days", ($day - intval(date('w')) + 7) % 7));
  return strtotime(date('Y-m-d ', $date_today) . $daytime);
}

function get_number_of_participants()
{
  global $wpdb;
  return $wpdb->get_var(
    $wpdb->prepare(
    "
    SELECT  COUNT(user_id)
    FROM  wp_stammtisch
    WHERE  date = %s
    ", strftime('%Y-%m-%d', get_next_stammtisch_timestamp())
   ));
}

function can_participate()
{
  return get_next_stammtisch_timestamp() - time() >= 60 * 60 * STAMMTISCH_DEFAULT_LOCK_HOURS;
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

function get_participation_for_current_user()
{
  global $wpdb;
  $table_name = $wpdb->prefix . "stammtisch";
  $results = $wpdb->get_row(
    $wpdb->prepare(
    "
    SELECT user_id, arrives_later, date
    FROM $table_name
    WHERE user_id = %d
    AND date = %s
    ", get_current_user_id(), strftime('%Y-%m-%d', get_next_stammtisch_timestamp())
    ));
  return $results;
}

function add_participation_for($user_id, $later)
{
  global $wpdb;
  $old = $wpdb->show_errors;
  $wpdb->hide_errors();
  $table_name = $wpdb->prefix . "stammtisch";
  $inserted = $wpdb->insert($table_name,
          array(
                'user_id' => $user_id,
                'date' => strftime('%Y-%m-%d', get_next_stammtisch_timestamp()),
                'arrives_later' => intval($later)
         ),
          array(
                '%d',
                '%s',
                '%d'
         )
   );
  if (!$inserted){
    // row already there, try updating with new arrives_later value
    $wpdb->update($table_name,
      // DATA
      array(
          'arrives_later' => intval($later)
      ),
      // WHERE
      array( 'user_id' => $user_id
      ),
      // DATA FORMAT
      array(
          '%d'
      ),
      // WHERE FORMAT
      array( '%d' )
      );
  }
  if ($old){
    $wpdb->show_errors();
  }
}

function join_regulars_table($later) {
  if (can_participate()) {
    add_participation_for(get_current_user_id(), $later);
    return true;
  }
  return false;
}

function cancel_participation_for($user_id)
{
  global $wpdb;
  $table_name = $wpdb->prefix . "stammtisch";
  if (is_admin()) {
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

function cancel_participation() {
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
