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

// Add shortcode for latest event
add_shortcode('fsinf_current_event_booking', 'fsfin_events_booking_form');
add_shortcode('fsinf_current_event_details', 'fsfin_events_details');

// Add JS to Admin head
add_action('admin_head', 'fsinf_events_js');

// Send HTML Emails
#add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));

/* Database */
function fsinf_events_install() {
  global $wpdb;
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  /* Event table */
  $fsinf_events_table = $wpdb->prefix . "fsinf_events";
  $sql_fsinf_events_table = "CREATE TABLE  $fsinf_events_table (
                            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                            title VARCHAR( 255 ) NOT NULL ,
                            place VARCHAR( 64 ) NOT NULL ,
                            starts_at DATETIME NOT NULL ,
                            ends_at DATETIME NOT NULL ,
                            description TEXT NOT NULL ,
                            camping TINYINT( 1 ) NOT NULL,
                            max_participants MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
                            fee MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
                            ) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_unicode_ci;
  ";
  dbDelta($sql_fsinf_events_table);

  /* Participant table */
  $fsinf_participants_table = $wpdb->prefix . "fsinf_participants";
  $sql_fsinf_participants_table = "CREATE TABLE  $fsinf_participants_table (
                                  mail_address VARCHAR( 127 ) NOT NULL ,
                                  event_id INT UNSIGNED NOT NULL ,
                                  first_name VARCHAR( 255 ) NOT NULL ,
                                  last_name VARCHAR( 255 ) NOT NULL ,
                                  mobile_phone VARCHAR( 255 ) NOT NULL ,
                                  semester TINYINT UNSIGNED NOT NULL ,
                                  bachelor TINYINT( 1 ) NOT NULL ,
                                  has_car TINYINT( 1 ) NOT NULL ,
                                  has_tent TINYINT( 1 ) NOT NULL ,
                                  car_seats TINYINT UNSIGNED NOT NULL ,
                                  tent_size TINYINT UNSIGNED NOT NULL ,
                                  notes TEXT NOT NULL ,
                                  admitted TINYINT( 1 ) NOT NULL ,
                                  paid TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  0,
                                  PRIMARY KEY (  mail_address ,  event_id )
                                  ) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_unicode_ci;
  ";
  dbDelta($sql_fsinf_participants_table);

  // foreach (stammtisch_options() as $name => $default) {
  //   add_option($name, $default);
  // }
}

// action function for above hook
function fsinf_events_add_pages() {

    // Add a new top-level menu (ill-advised):
    add_menu_page(__('FSInf-Events','fsinf-events'), __('FSInf-Events','fsinf-events'), 'manage_options', 'fsinf-events-top-level-handle', 'fsinf_events_toplevel_page' );

    // Add a submenu to the custom top-level menu:
    add_submenu_page('fsinf-events-top-level-handle', __('Neues Event','fsinf-events-new'), __('Neues Event','fsinf-events-new'), 'manage_options', 'fsinf-add-event-page', 'fsinf_add_event_page');

    // Add a second submenu to the custom top-level menu:
    add_submenu_page('fsinf-events-top-level-handle', __('Alle Events','fsinf-events-all'), __('Alle Events','fsinf-events-all'), 'manage_options', 'fsinf-all-events-page', 'fsinf_all_events_page');
}

function fsinf_alert_info($input_string)
{
  echo "<div class='alert alert-info span4'>
          $input_string
        </div>";
}

function formatted_fee_for($event)
{
  $fee_string = ($event->fee / 100) . ',' . ($event->fee % 100) . ($event->fee % 10);
  $fee = floatval($fee_string);
  setlocale(LC_MONETARY, 'de_DE');
  return money_format('%#3.2i', $fee);
}

function is_admitted($participant)
{
  return intval($participant->admitted);
}

// mt_toplevel_page() displays the page content for the custom FSInf-Events menu
function fsinf_events_toplevel_page() {
    echo "<h2>" . __( 'FSInf-Events', 'fsinf-events' ) . "</h2>";
    echo "<div class=row>";
    if (array_key_exists('participation_paid_by', $_POST) && participation_paid_by() == 1){
      fsinf_alert_info("Benutzer hat bezahlt.");
    }
    if (array_key_exists('participation_not_paid_by', $_POST) && participation_not_paid_by() == 1){
      fsinf_alert_info("Benutzer hat nicht mehr bezahlt.");
    }
    if (array_key_exists('participation_admitted_for', $_POST) && participation_admitted_for() == 1){
      fsinf_alert_info("Benutzer ist zugelassen.");
    }
    if (array_key_exists('participation_not_admitted_for', $_POST) && participation_not_admitted_for() == 1){
      fsinf_alert_info("Benutzer ist nicht mehr zugelassen.");
    }
    if (array_key_exists('participation_cancel_for', $_POST) && participation_cancel_for() == 1){
      fsinf_alert_info("Benutzer nimmt nicht mehr teil.");
    }

?>
</div>
<div class="row">
    <div id="fsinf-events-list" class="span8">
      <?php
          $current_event = fsinf_get_current_event();
      ?>
            <h3>Aktuelles Event: <?= htmlspecialchars($current_event->title)?> <small>am <?php setlocale(LC_TIME, "de_DE"); echo strftime("%d. %b %G",strtotime(htmlspecialchars($current_event->starts_at)))?></small></h3>
<?php

            $registrations = fsinf_get_registrations();
            $number_registrations = count($registrations);

            $admitted_registrations = array_filter($registrations, 'is_admitted');
            $number_admitted_registrations = count($admitted_registrations);

            $number_seats = 0;
            foreach ($registrations as $registrant) {
              $number_seats += $registrant->car_seats;
            }

            $number_seats_admitted = 0;
            foreach ($admitted_registrations as $registrant) {
              $number_seats_admitted += $registrant->car_seats;
            }
?>          <ul>
              <li>Teilnahmegebühr: <?= formatted_fee_for($current_event); ?></li>
              <li>Maximale Teilnehmerzahl: <?= $current_event->max_participants ?></li>
              <li>Anzahl Teilnahmen insgesamt: <?= $number_registrations ?></li>
              <li>Anzahl Teilnahmen zugelassen: <?= $number_admitted_registrations ?></li>
              <li>Anzahl Sitzplätze insgesamt: <?= $number_seats ?></li>
              <li>Anzahl Sitzplätze zugelassen: <?= $number_seats_admitted ?></li>
            </ul>
<?php
            if ($number_seats_admitted >= $number_admitted_registrations):
?>            <p class="alert alert-success span3">Genug Sitzplätze</p>
<?php       else:
?>            <p class="alert alert-error span3">Nicht genug Sitzplätze</p>
<?php
            endif;
            if ($number_registrations > 0) {
?>
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>Bearbeiten</th>
                  <th>Teilnehmer</th>
                  <th>E-Mail</th>
                  <th>Handy</th>
                  <th>Semester</th>
                  <th>Auto</th>
                  <th>Zelt</th>
                  <th>Zugelassen</th>
                  <th>Bezahlt</th>
                  <th>Anmerkungen</th>
                </tr>
              </thead>
              <tbody>
<?php
              foreach ($registrations as $participant):
?>
                  <tr>
                    <td>
                        <form action="" method="post">
                      <div class="btn-group">
                          <?php
                            if (!$participant->paid):
                          ?>
                            <button type="submit" class="btn btn-small" title="Hat bezahlt" value="<?= htmlspecialchars($participant->mail_address);?>" name="participation_paid_by"><i class="icon-shopping-cart"></i></button>
                          <?php
                            else:
                          ?>
                            <button type="submit" class="btn btn-small" title="Hat nicht bezahlt" value="<?= htmlspecialchars($participant->mail_address);?>" name="participation_not_paid_by"><i class="icon-remove-sign"></i></button>
                          <?php
                            endif;
                          ?>
                          <?php
                            if (!$participant->admitted):
                          ?>
                            <button type="submit" class="btn btn-small" title="Zugelassen" value="<?= htmlspecialchars($participant->mail_address);?>" name="participation_admitted_for"><i class="icon-ok"></i></button>
                          <?php
                            else:
                          ?>
                            <button type="submit" class="btn btn-small" title="Nicht mehr zugelassen" value="<?= htmlspecialchars($participant->mail_address);?>" name="participation_not_admitted_for"><i class="icon-remove"></i></button>
                          <?php
                            endif;
                          ?>
                          <button type="submit" class="btn btn-danger btn-small" title="Entfernen" value="<?= htmlspecialchars($participant->mail_address);?>" name="participation_cancel_for"><i class="icon-trash icon-white"></i></button>
                      </div>
                        </form>
                    </td>
                    </td>
                    </td>
                    <td>
                      <?= $participant->first_name .' '.$participant->last_name; ?>
                    </td>
                    <td>
                      <?= $participant->mail_address; ?>
                    </td>
                    <td>
                      <?= $participant->mobile_phone; ?>
                    </td>
                    <td>
                      <?= $participant->semester <= 6 ? $participant->semester.'.' : 'Höheres'?> Sem. <?= $participant->bachelor == 1 ? 'Bachelor' : 'Master' ?>
                    </td>
                    <td>
<?php
                    if ($participant->has_car == 1) :
?>                  Ein Auto mit <?= htmlspecialchars($participant->car_seats)?> <?= htmlspecialchars($participant->car_seats) == 1 ? 'Sitz' : 'Sitzen'?>
<?php
                    else:
?>                  Kein Auto
<?php
                    endif;
?>
                    </td>
                    <td>
<?php
                    if ($participant->has_tent == 1) :
?>                  Ein Zelt mit <?= htmlspecialchars($participant->tent_size)?> <?= htmlspecialchars($participant->tent_size) == 1 ? 'Schlafplatz' : 'Schlafplätzen'?>
<?php
                    else:
?>                  Kein Zelt
<?php
                    endif;
?>
                    </td>
                    <td>
                      <?= $participant->admitted == 1 ? 'Yep' : 'Nope'; ?>
                    </td>
                    <td>
                      <?= $participant->paid == 1 ? 'Yep' : 'Nope'; ?>
                    </td>
                    <td>
                      <pre><?= $participant->notes; ?></pre>
                    </td>
                  </tr>
<?php         endforeach;
            } else {
?>
              <p>Das aktuelle Event hat noch keine Teilnehmer.</p>
<?php
            }
?>
              </tbody>
            </table>
          </div>
      </div>
<?php
}

function participation_paid_by()
{
if (is_admin()){
  $current_event = fsinf_get_current_event();
  if (array_key_exists('participation_paid_by', $_POST)){
    global $wpdb;
    $changed = $wpdb->update(
        FSINF_PARTICIPANTS_TABLE,
        array('paid' => 1),
        array('mail_address' => $_POST['participation_paid_by'], 'event_id' => $current_event->id),
        array('%d'),
        array('%s', '%d')
    );
    return $changed;
  }
}
}

function participation_not_paid_by()
{
if (is_admin()){
  $current_event = fsinf_get_current_event();
  if (array_key_exists('participation_not_paid_by', $_POST)){
    global $wpdb;
    $changed = $wpdb->update(
        FSINF_PARTICIPANTS_TABLE,
        array('paid' => 0),
        array('mail_address' => $_POST['participation_not_paid_by'], 'event_id' => $current_event->id),
        array('%d'),
        array('%s', '%d')
    );
    return $changed;
  }
}
}

function participation_admitted_for()
{
  if (is_admin()){
  $current_event = fsinf_get_current_event();
  if (array_key_exists('participation_admitted_for', $_POST)){
    global $wpdb;
    $changed = $wpdb->update(
        FSINF_PARTICIPANTS_TABLE,
        array('admitted' => 1),
        array('mail_address' => $_POST['participation_admitted_for'], 'event_id' => $current_event->id),
        array('%d'),
        array('%s', '%d')
    );
    return $changed;
  }
}
}

function participation_not_admitted_for()
{
if (is_admin()){
  $current_event = fsinf_get_current_event();
  if (array_key_exists('participation_not_admitted_for', $_POST)){
    global $wpdb;
    $changed = $wpdb->update(
        FSINF_PARTICIPANTS_TABLE,
        array('admitted' => 0),
        array('mail_address' => $_POST['participation_not_admitted_for'], 'event_id' => $current_event->id),
        array('%d'),
        array('%s', '%d')
    );
    return $changed;
  }
}
}

/*
DELETE FROM `wp_fsinf_participants` WHERE `mail_address` = \'d@d.d\' AND `wp_fsinf_participants`.`event_id` = 1
DELETE FROM 'wp_fsinf_participants' WHERE mail_address = 'd@d.d' AND event_id = 1
 */

function participation_cancel_for()
{
  if (is_admin()){
    $current_event = fsinf_get_current_event();
    if (array_key_exists('participation_cancel_for', $_POST)){
    global $wpdb;
    $table_name = $wpdb->prefix . "fsinf_participants";
    $changed = $wpdb->query(
            $wpdb->prepare(
              "DELETE FROM $table_name
               WHERE mail_address = %s
               AND event_id = %d
              ",
              $_POST['participation_cancel_for'], $current_event->id
              )
    );
    return $changed;
  }
  }
}

function fsinf_get_registrations()
{
  global $wpdb;
  $current_event = fsinf_get_current_event();
  $results = $wpdb->get_results(
    $wpdb->prepare(sprintf(
      "SELECT  mail_address, event_id, first_name, last_name, mobile_phone, semester, bachelor, has_car, has_tent, car_seats, tent_size, notes, admitted, paid
       FROM  %s
       WHERE  event_id = %d
       ORDER BY semester ASC
      ", FSINF_PARTICIPANTS_TABLE, $current_event->id
      )
    )
    );
  return $results;
}

// mt_sublevel_page() displays the page content for the first submenu
// of the custom FSInf-Events menu
function fsinf_add_event_page() {
    echo "<h2>" . __( 'Create a new Event', 'fsinf-events' ) . "</h2>";
}

function fsinf_get_coming_events($count)
{
  global $wpdb;
  return $wpdb->get_results($wpdb->prepare(sprintf(
    "SELECT id, title, place, starts_at, ends_at, description, camping, max_participants, fee
     FROM %s
     WHERE starts_at > NOW()
     ORDER BY starts_at ASC
     LIMIT %d",
    FSINF_EVENTS_TABLE, $count
  )));
}
function fsinf_get_past_events($count)
{
  global $wpdb;
  return $wpdb->get_results($wpdb->prepare(sprintf(
    "SELECT id, title, place, starts_at, ends_at, description, camping, max_participants, fee
     FROM %s
     WHERE starts_at < NOW()
     ORDER BY ends_at DESC
     LIMIT %d",
    FSINF_EVENTS_TABLE, $count
  )));
}

function fsinf_remove_event()
{
  if (is_admin()){
    if (array_key_exists('fsinf_remove_event', $_POST)){
      global $wpdb;
      $ok = $wpdb->query($wpdb->prepare(sprintf(
        "DELETE FROM %s
         WHERE id = %d
        ", FSINF_EVENTS_TABLE, $_POST['fsinf_remove_event']
        )));
      if($ok):
?>
      <div class="alert alert-success">
        Event erfolgreich entfernt.
      </div>
<?php
      else:
?>
      <div class="alert alert-error">
        Kein Event mit dieser ID vorhanden.
      </div>
<?php
      endif;
    }
  }
}

// mt_sublevel_page2() displays the page content for the second submenu
// of the custom FSInf-Events menu
function fsinf_all_events_page() {

    $current_event = fsinf_get_current_event();
    ?>
    <h2>Alle Events</h2>
    <div class="row">
      <div class="span12">
        <h3>Aktuelles Event: <?= $current_event->title ?> <small>am <?php setlocale(LC_TIME, "de_DE"); echo strftime("%d. %b %G",strtotime(htmlspecialchars($current_event->starts_at)))?></h3>
      </div>
      <div class="span12">
<?php
      // Process requests
      if (array_key_exists('fsinf_remove_event', $_POST)){
        fsinf_remove_event();
      }
?>
        <h3>Kommende 5 Events</h3>
        <table class="table table-hover">
          <thead>
            <tr>
            <th>Bearbeiten</th>
            <th>Titel</th>
            <th>Beginn</th>
            <th>Ende</th>
            <th>Ort</th>
            <th>Beschreibung</th>
            <th>Art</th>
            <th>Max. Teilnehmer</th>
            <th>Gebühr</th>
          </tr>
          </thead>
          <tbody>
<?php
          $coming_events = fsinf_get_coming_events(5);
          foreach ($coming_events as $event) :
?>
            <tr>
              <td>
                <form action="" method="post">
                  <button type="submit" class="btn btn-danger btn-small" title="Entfernen" value="<?=$event->id?>" name="fsinf_remove_event"><i class="icon-trash icon-white"></i></button>
                </form>
              </td>
              <td><?= $event->title ?></td>
              <td><?= $event->starts_at ?></td>
              <td><?= $event->ends_at ?></td>
              <td><?= $event->place ?></td>
              <td><?= $event->description ?></td>
              <td><?= $event->camping == 1 ? 'Zelten' : 'Hütte' ?></td>
              <td><?= $event->max_participants ?></td>
              <td><?= formatted_fee_for($event) ?></td>
            </tr>
<?php
          endforeach;
?>
          </tbody>
        </table>
      </div>
      <div class="span12">
        <h3>Vergangene 5 Events</h3>
        <table class="table table-hover">
          <thead>
            <tr>
            <th>Titel</th>
            <th>Beginn</th>
            <th>Ende</th>
            <th>Ort</th>
            <th>Beschreibung</th>
            <th>Art</th>
            <th>Max. Teilnehmer</th>
            <th>Gebühr</th>
          </tr>
          </thead>
          <tbody>
<?php
          $past_events = fsinf_get_past_events(5);
          foreach ($past_events as $event) :
?>
            <tr>
              <td><?= $event->title ?></td>
              <td><?= $event->starts_at ?></td>
              <td><?= $event->ends_at ?></td>
              <td><?= $event->place ?></td>
              <td><?= $event->description ?></td>
              <td><?= $event->camping == 1 ? 'Zelten' : 'Hütte' ?></td>
              <td><?= $event->max_participants ?></td>
              <td><?= formatted_fee_for($event) ?></td>
            </tr>
<?php
          endforeach;
?>
          </tbody>
        </table>
      </div>
    </div>
<?php
}


function fsinf_events_config()
{
  return array(
    'events' => array(),
    'participants' => array(
      'mail_address' => array(
        'type' => 'string',
        'max_length' => 127,
        'validation' => 'fsinf_validate_email'
      ),
      'first_name' => array(
        'type' => 'string',
        'max_length' => 255,
        'validation' => 'fsinf_validate_ne_string'
      ),
      'last_name' => array(
        'type' => 'string',
        'max_length' => 255,
        'validation' => 'fsinf_validate_ne_string'
      ),
      'mobile_phone' => array(
        'type' => 'string',
        'max_length' => 255,
        'validation' => 'fsinf_validate_ne_string'
      ),
      'semester' => array(
        'type' => 'int',
        'max_value' => 127,
        'validation' => 'fsinf_validate_semester'
      ),
      'bachelor' => array(
        'type' => 'int',
        'max_value' => 1,
        'validation' => 'fsinf_validate_bool'
      ),
      'has_car' => array(
        'type' => 'int',
        'max_value' => 1,
        'default' => false,
        'validation' => 'fsinf_validate_bool'
      ),
      'has_tent' => array(
        'type' => 'int',
        'max_value' => 1,
        'default' => false,
        'validation' => 'fsinf_validate_bool'
      ),
      'car_seats' => array(
        'type' => 'int',
        'max_value' => 127,
        'default' => 0,
      ),
      'tent_size' => array(
        'type' => 'int',
        'max_value' => 127,
        'default' => 0,
      ),
      'notes' => array(
        'type' => 'string'
      )
    )
  );
}
// Function from Wordpress Source Code v. 3.4.2
function fsinf_is_email( $email) {
        // Test for the minimum length the email can be
        if ( strlen( $email ) < 3 ) return false;

        // Test for an @ character after the first position
        if ( strpos( $email, '@', 1 ) === false ) return false;

        // Split out the local and domain parts
        list( $local, $domain ) = explode( '@', $email, 2 );

        // LOCAL PART
        // Test for invalid characters
        if ( !preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local ) ) return false;

        // DOMAIN PART
        // Test for sequences of periods
        if ( preg_match( '/\.{2,}/', $domain ) ) return false;

        // Test for leading and trailing periods and whitespace
        if ( trim( $domain, " \t\n\r\0\x0B." ) !== $domain ) return false;

        // Split the domain into subs
        $subs = explode( '.', $domain );

        // Assume the domain will have at least two subs
        if ( 2 > count( $subs ) ) return false;

        // Loop through each sub
        foreach ( $subs as $sub ) {
                // Test for leading and trailing hyphens and whitespace
                if ( trim( $sub, " \t\n\r\0\x0B-" ) !== $sub ) return false;
                // Test for invalid characters
                if ( !preg_match('/^[a-z0-9-]+$/i', $sub ) ) return false;
        }
        return true;
}

function fsinf_validate_email($address)
{
  $ne = fsinf_validate_ne_string($address);
  if(!$ne[0]) return $ne;
  $ok = fsinf_is_email($ne[1]);
  return array($ok, $ok ? $ne[1] : "Ungültige Mail-Adresse.");
}

function fsinf_validate_ne_string($str)
{
  $ok = strlen($str) > 0;
  return array($ok, $ok ? $str : "Eingabe darf nicht leer sein.");
}

function fsinf_validate_semester($semester)
{
  $ok = $semester > 0;
  return array($ok, $ok ? ($semester < 7 ? $semester : 99)
    : "Unbekanntes Semester.");
}

function fsinf_validate_bool($value)
{
  return array(true, $value == 1);
}

function error_class($field_name, $errors)
{
  return array_key_exists($field_name, $errors) ? 'error' : '';
}

function fsinf_field_contents($field_name, $errors)
{
  return empty($errors) || !array_key_exists($field_name, $_POST) ? '' : htmlspecialchars($_POST[$field_name]);
}

function fsinf_get_current_event()
{
    // Get current event
  global $wpdb;
  return $wpdb->get_row(sprintf(
    "SELECT id, title, place, starts_at, ends_at, description, camping, max_participants, fee
     FROM %s
     WHERE starts_at > NOW()
     ORDER BY starts_at ASC
     LIMIT 1",
    FSINF_EVENTS_TABLE
  ));
}

function fsinf_get_registration_params()
{
  $config = fsinf_events_config();
  $params = array();
  foreach (array_keys($config['participants']) as $field) {
      if (array_key_exists($field, $_POST)) {
        $params[$field] = $_POST[$field];
      }
  }
  return $params;
}

function fsinf_events_register()
{
  $validated = array();
  $errors = array();

  $config = fsinf_events_config();
  foreach ($config['participants'] as $field => $spec) {
    if ($spec['type'] == 'string') {
      if (array_key_exists($field, $_POST) && is_string($_POST[$field])) {
        $value = trim($_POST[$field]);
        $ok = true;
        if (array_key_exists('validation', $spec)) {
          $valid = call_user_func($spec['validation'], $value);
          if ($valid[0]) {
            $value = $valid[1];
          } else {
            $ok = false;
            $errors[$field] = $valid[1];
          }
        }

        if ($ok && array_key_exists('max_length', $spec)
            && strlen($value) > $spec['max_length']) {
          $errors[$field] = "Eingabe darf nicht länger als {$spec['max_length']} Zeichen sein.";
          $ok = false;
        }

        if ($ok) $validated[$field] = $value;
      } else {
        $errors[$field] = "Eingabe fehlt.";
      }
    } elseif ($spec['type'] == 'int') {
      if (array_key_exists($field, $_POST) && is_string($_POST[$field]) && strlen(trim($_POST[$field])) !== 0) {
        $value = trim($_POST[$field]);
        if (!ctype_digit($value)) {
          $errors[$field] = "Bitte nur Ganzzahlen eingeben.";
        } else {
          $value = intval($value);
          if(array_key_exists('max_value', $spec) && $value > $spec['max_value']) {
            $errors[$field] = "Der eingegebene Wert darf nicht größer als {$spec['max_value']} sein.";
          } else {
            $ok = true;
            if(array_key_exists('validation', $spec)) {
              $valid = call_user_func($spec['validation'], $value);
              if($valid[0]) {
                $value = $valid[1];
              } else {
                $ok = false;
                $errors[$field] = $valid[1];
              }
            }

            if ($ok) {
              $validated[$field] = $value;
            }
          }
        }
      } elseif (array_key_exists('default', $spec)) {
        $validated[$field] = $spec['default'];
      } else {
        $errors[$field] = "Eingabe fehlt.";
      }
    } else {
      $errors[$field] = "WTF?";
    }
  }

  return empty($errors) ? fsinf_save_registration($validated) : $errors;
}

function fsinf_save_registration($fields)
{
  global $wpdb;
  $config = fsinf_events_config();
  $cfg = $config['participants'];
  $types = array();
  foreach(array_keys($fields) as $name)
    $types[$name] = $cfg[$name]['type'] === 'int' ? '%d' : '%s';

  $current_event = fsinf_get_current_event();
  $fields['event_id'] = $current_event->id;
  $types['event_id'] = '%d';

  $fields['admitted'] = intval(in_array($fields['semester'], array(1,2)));
  $types['admitted'] = '%d';

  $result = $wpdb->insert(FSINF_PARTICIPANTS_TABLE,
          $fields,
          $types
    );
  #echo '<pre>'.print_r($fields, true).'</pre>';
  send_registration_mail($fields);
  return array(); #TODO ???
}

function fsinf_bank_account_information()
{
?>  <dl>
      <dt>Inhaber:</dt>
      <dd>hier ausgeben</dd>
    </dl>
<?php
}

# TODO: probably fix b/c it's very late
function send_registration_mail($fields){
  $current_event = fsinf_get_current_event();
  $fee = formatted_fee_for($current_event);
  # Array form of headers can set CC (e.g. to event admin)
  $headers = 'From: Fachschaft Informatik Uni Konstanz <fachschaft@inf.uni-konstanz.de>' . "\r\n";

  $topic = 'Anmeldung zum Event: ' .htmlspecialchars($current_event->title);

  $semester_string = htmlspecialchars($fields['semester']) <= 6 ? htmlspecialchars($fields['semester']).'.' : 'Höheres';
  $semester_string .= ' Semester im ';
  $semester_string .= htmlspecialchars($fields['bachelor']) == 1 ? 'Bachelor' : 'Master';

  $message = array();
  $message[] = "Yay! Du hast dich soeben erfolgreich zum Event ".htmlspecialchars($current_event->title)." angemeldet.";
  $message[] = "";
  $message[] = "Bitte überweise $fee auf unten stehendes Konto.";
  $message[] = "\n";
  $message[] = "==== Konto";
  $message[] = "Inhaber:";
  $message[] = "Kontonummer:";
  $message[] = "BLZ:";
  $message[] = "Institut:";
  $message[] = "=============";
  $message[] = "\n";
  $message[] = "Deine Daten";
  $message[] = '------------';
  $message[] = "Name: " . htmlspecialchars($fields['first_name']).' '. htmlspecialchars($fields['last_name']);
  $message[] = "Handy-Nummer: " . htmlspecialchars($fields['mobile_phone']);
  $message[] = "Semester: " . $semester_string;

  if (array_key_exists('has_car', $fields)) :
    if ($fields['has_car'] == 1) :
      $car_string = 'Ein Auto mit ';
      $car_string .= htmlspecialchars($fields['car_seats']);
      $car_string .= htmlspecialchars($fields['car_seats']) == 1 ? ' Sitz' : ' Sitzen';
      $message[] = $car_string;
    endif;
  else:
    $message[] = 'Kein Auto';
  endif;

  if (array_key_exists('has_tent', $fields)) :
    if ($fields['has_tent'] == 1) :
      $tent_string = 'Ein Zelt mit ';
      $tent_string .= htmlspecialchars($fields['tent_size']);
      $tent_string .= htmlspecialchars($fields['tent_size']) == 1 ? ' Schlafplatz' : ' Schlafplätzen';
      $message[] = $tent_string;
    endif;
  else:
      $message[] = 'Kein Zelt';
  endif;
  if (array_key_exists('notes', $fields)) :
    $notes = htmlspecialchars($fields['notes']);
  else:
    $notes = 'Keine Nachricht';
  endif;
  $message[] = 'Deine Nachricht an uns: ' . $notes;
  $message[] = '---';
  # TODO: Event Details mit verschicken...
  $message[] = "\n\n";
  $message[] = 'Wir freuen uns auf Dich';
  $message[] = 'Deine Fachschaft Informatik';


  wp_mail($fields['mail_address'], $topic, implode("\r\n",$message), $headers);
}

function fsinf_print_success_message(){
  $current_event = fsinf_get_current_event();
  $fee = formatted_fee_for($current_event);
?>  <div class="alert alert-success alert-block">
      <a href="#" class="close" data-dismiss="alert">×</a>
      <h4>Erfolgreich angemeldet!</h4>
      <p>Du hast dich soeben erfolgreich für das Event
        <b><?=htmlspecialchars($current_event->title)?></b> angemeldet.</p>
        <p>Bitte zahle die Teilnahmegebühr von <b><?=$fee?></b> auf untenstehendes Konto ein.</p>
    </div>
        <h4>Kontodaten</h4>
<?php
        fsinf_bank_account_information();
?>
        <p>Folgende Informationen
        wurden dir auch an
        <b>
          <?= array_key_exists('mail_address',$_POST) ? htmlspecialchars($_POST['mail_address']) : 'keine E-Mail-Adresse angegeben?' ?>
        </b> gesendet:
 <?php
        $registration_data = fsinf_get_registration_params();
?>
        <dl>
          <dt>Name</dt>
          <dd><?= htmlspecialchars($registration_data['first_name'])?> <?= htmlspecialchars($registration_data['last_name'])?></dd>
          <dt>Handy-Nummer</dt>
          <dd><?= htmlspecialchars($registration_data['mobile_phone'])?></dd>
          <dt>Semester</dt>
          <dd><?= htmlspecialchars($registration_data['semester']) <= 6 ? htmlspecialchars($registration_data['semester']).'.' : 'Höheres' ?> Semester im <?= htmlspecialchars($registration_data['bachelor']) == 1 ? 'Bachelor' : 'Master' ?></dd>
          <dt>Auto</dt>
          <dd>
<?php
        if (array_key_exists('has_car', $registration_data)) :
          if ($registration_data['has_car'] == 1) :
            ?>
            Ein Auto mit <?= htmlspecialchars($registration_data['car_seats'])?> <?= htmlspecialchars($registration_data['car_seats']) == 1 ? 'Sitz' : 'Sitzen'?>
          <?php
          endif;
        else:
?>        Kein Auto
<?php
        endif;
?>
          </dd>
          <dt>Zelt</dt>
          <dd>
<?php
        if (array_key_exists('has_tent', $registration_data)) :
          if ($registration_data['has_tent'] == 1) :
            ?>
            Ein Zelt mit <?= htmlspecialchars($registration_data['tent_size'])?> <?= htmlspecialchars($registration_data['tent_size']) == 1 ? 'Schlafplatz' : 'Schlafplätzen'?>
          <?php
          endif;
        else:
?>        Kein Zelt
<?php
        endif;
?>
          </dd>
          <dt>Deine Nachricht an uns</dt>
          <dd>
<?php
        if (array_key_exists('notes', $registration_data)) :
            echo htmlspecialchars($registration_data['notes']);
        else:
?>        Keine Nachricht
<?php
        endif;
?>
          </dd>
        </dl>
      </p>
  <hr/>
<?php
}

/*
    FS Inf Events booking form.
    Embed with shortcode: [fsinf_current_event_booking]
 */

function fsfin_events_booking_form()
{
  $curr_event = fsinf_get_current_event();

  if(is_null($curr_event)) {
?>  <div class="alert alert-info">
      <a class="close" data-dismiss="alert">×</a>
      <p>Aktuell ist kein Event eingetragen.</p>
    </div>
<?php
    return;
  }

  $errors = array();
  if (array_key_exists('fsinf_events_register', $_POST)) {
    $errors = fsinf_events_register();
    if(count($errors) == 0):
      fsinf_print_success_message();
    else:
?>    <div class="alert alert-error">
        Das Formular enthält noch fehlerhafte Eingaben. Bitte korrigiere diese und schicke es erneut ab.
      </div>
<?php
    endif;
  }

?>  <h2>Anmeldung zum Event: <?= htmlspecialchars($curr_event->title) ?></h2>
      <form method="POST" action="" class="form-horizontal">

        <fieldset>
          <legend>Persönliches</legend>
<?php
$field_name = 'first_name';
?>        <div class="control-group <?= error_class($field_name, $errors) ?>">
          <label class="control-label" for="<?=$field_name?>">Vorname</label>
          <div class="controls">
          <input type="text" name="<?=$field_name?>" id="<?=$field_name?>" maxlength="255" value="<?= fsinf_field_contents($field_name, $errors) ?>"/>
          <span class="help-inline"><?= @$errors[$field_name] ?></span>
          </div>
        </div>

<?php
$field_name = 'last_name';
?>        <div class="control-group <?= error_class($field_name, $errors) ?>">
          <label class="control-label" for="<?=$field_name?>">Nachname</label>
          <div class="controls">
          <input type="text" name="<?=$field_name?>" id="<?=$field_name?>" maxlength="255" value="<?= fsinf_field_contents($field_name, $errors) ?>"/>
          <span class="help-inline"><?= @$errors[$field_name] ?></span>
          </div>
        </div>

<?php
$field_name = 'mail_address';
?>        <div class="control-group <?= error_class($field_name, $errors) ?>">
          <label class="control-label" for="<?=$field_name?>">E-Mail-Adresse</label>
          <div class="controls">
          <input type="email" name="<?=$field_name?>" id="<?=$field_name?>" maxlength="127" value="<?= fsinf_field_contents($field_name, $errors) ?>"/>
          <span class="help-inline"><?= @$errors[$field_name] ?></span>
          </div>
        </div>

<?php
$field_name = 'mobile_phone';
?>        <div class="control-group <?= error_class($field_name, $errors) ?>">
          <label class="control-label" for="<?=$field_name?>">Handy-Nummer</label>
          <div class="controls">
          <input type="text" name="<?=$field_name?>" id="<?=$field_name?>" maxlength="255" value="<?= fsinf_field_contents($field_name, $errors) ?>"/>
          <span class="help-inline"><?= @$errors[$field_name] ?></span>
          </div>
        </div>
      </fieldset>
        <fieldset>
          <legend>Studium</legend>
<?php
$field_name = 'semester';
?>        <div class="control-group <?= error_class($field_name, $errors) ?>">
          <label class="control-label" for="<?=$field_name?>">Semester</label>
          <div class="controls">
        <select name="<?=$field_name?>" id="<?=$field_name?>">
<?php
$semesters = array(1, 2, 3, 4, 5, 6, 99);
$selected = array_key_exists($field_name, $errors) ? 1 : intval($_POST[$field_name]);
foreach($semesters as $i):
?>                <option value="<?=$i?>"<?= $i == $selected ? ' selected="selected"' : '' ?>><?= $i <= 6 ? "$i. Semester" : 'Anderes Semester (>6)' ?></option>
<?php
endforeach;
?>            </select>
          <span class="help-inline"><?= @$errors[$field_name] ?></span>
          </div>
        </div>
<?php
$field_name = 'bachelor';
$bachelor = empty($errors) || array_key_exists($field_name, $errors) || intval($_POST[$field_name]) == 1;
?>        <div class="control-group <?= error_class($field_name, $errors) ?>">
            <div class="controls">
              <label class="radio" >Bachelor
                <input type="radio" name="bachelor" value="1"<?= $bachelor ? ' checked="checked"' : ''?>/>
              </label>
              <label class="radio" >Master
                <input type="radio" name="bachelor" value="0"<?= !$bachelor ? ' checked="checked"' : ''?>/>
              </label>
            </div>
          </div>
        </fieldset>

        <fieldset>
          <legend>Organisatorisches</legend>

<?php
$field_name = 'has_car';
$selected = !empty($errors) && array_key_exists($field_name, $_POST);
?>        <div class="control-group <?= error_class($field_name, $errors) ?>">
            <div class="controls">
              <label class="checkbox inline">Ich habe ein Auto und kann damit zur Hütte fahren.
                <input type="checkbox" name="<?=$field_name?>" value="1" <?= $selected ? ' checked="checked"' : '' ?>/>
              </label>
            </div>
          </div>

<?php
$field_name = 'car_seats';
?>        <div class="control-group <?= error_class($field_name, $errors) ?>">
          <label class="control-label" for="<?=$field_name?>">Wie viele Plätze im Auto? Inkl. Fahrer</label>
          <div class="controls">
            <input type="number" name="<?=$field_name?>" id="<?=$field_name?>" value="<?= fsinf_field_contents($field_name, $errors) ?>" placeholder="z.B. 5" min="1" max="127"/>
            <span class="help-inline"><?= @$errors[$field_name] ?></span>
          </div></div>

<?php  if (intval($curr_event->camping)) :
?>
<?php
$field_name = 'has_tent';
$selected = !empty($errors) && array_key_exists($field_name, $_POST);
?>        <div class="control-group <?= error_class($field_name, $errors) ?>">
            <div class="controls">
              <label class="checkbox inline">Ich habe ein Zelt und kann dies mitnehmen.
                <input type="checkbox" name="<?=$field_name?>" value="1" <?= $selected ? ' checked="checked"' : '' ?>/>
              </label>
            </div>
          </div>

<?php
$field_name = 'tent_size';
?>        <div class="control-group <?= error_class($field_name, $errors) ?>">
          <label class="control-label" for="<?=$field_name?>">Wie viele Plätze im Zelt? Inkl. Fahrer</label>
          <div class="controls">
            <input type="number" name="<?=$field_name?>" id="<?=$field_name?>" value="<?= fsinf_field_contents($field_name, $errors) ?>" placeholder="z.B. 4" min="1" max="127"/>
            <span class="help-inline"><?= @$errors[$field_name] ?></span>
          </div></div>
<?php endif;
?>

<?php
$field_name = 'notes';
?>        <div class="control-group <?= error_class($field_name, $errors) ?>">
          <label class="control-label" for="<?=$field_name?>">Bemerkungen (Vegi o.ä)</label>
          <div class="controls">
            <textarea placeholder="z.B. Ich bin Vegetarier/Veganer/Pescetarier..." name="<?=$field_name?>" id="<?=$field_name?>" rows="4"><?= fsinf_field_contents($field_name, $errors) ?></textarea>
          </div>
        </div>
        </fieldset>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" name="fsinf_events_register" value="Anmelden">Anmelden</button>
          <button type="button" class="btn">Abbrechen</button>
        </div>
      </form>

<?php
  #echo '<pre>'.print_r($_POST, true).'</pre>';
}

function fsfin_events_details()
{
  $current_event = fsinf_get_current_event();
  if(!empty($current_event)){
?>
  <h3><?= $current_event->title ?></h3>
  <div class="row">
    <div class="span3">
  <dl class="dl-horizontal">
    <dt>Beginn</dt>
    <dd><?= strftime('%d.%m.%Y - %H:%M',strtotime($current_event->starts_at)) ?></dd>
    <dt>Ende</dt>
    <dd><?= strftime('%d.%m.%Y - %H:%M',strtotime($current_event->ends_at)) ?></dd>
    <dt>Ort</dt>
    <dd><?= $current_event->place ?></dd>
    <dt>Teilnahmegebühr</dt>
    <dd><?= formatted_fee_for($current_event)?></dd>
  </dl>
</div>
<div class="span4">
  <p><?= $current_event->description ?></p>
</div>
</div>
<h4>Teilnehmer</h4>
<?php
  $registrations = fsinf_get_registrations();
  $admitted_registrations = array_filter($registrations, 'is_admitted');
  $number_admitted_registrations = count($admitted_registrations);

  $empty_places = $current_event->max_participants - $number_admitted_registrations;
?>
  <span title="Angemeldet: <?= $number_admitted_registrations ?>">
<?php
  foreach ($admitted_registrations as $person) {
      if ($person->paid):
?>
      <span style="font-size: 32px; line-height: 32px; color: blue; margin-right: -9px;">
        <i class="icon-user"></i>
      </span>
<?php
    else:
?>
      <span style="font-size: 32px; line-height: 32px; color: red; margin-right: -9px;">
        <i class="icon-user"></i>
      </span>
<?php
    endif;
    }
?>
    </span>
    <span title="Frei: <?=$empty_places?>">
<?php
    for ($i=0; $i < $empty_places; $i++) {
?>
      <span style="font-size: 32px; line-height: 32px; color: green; margin-right: -9px;">
        <i class="icon-user"></i>
      </span>
<?php
    }
?>
</span>
<p>Blau: bezahlt | Rot: nicht bezahlt | Grün: frei</p>
<!--
  <table class="table table-hover">
          <thead>
            <tr>
            <th>Titel</th>
            <th>Beginn</th>
            <th>Ende</th>
            <th>Ort</th>
            <th>Beschreibung</th>
            <th>Art</th>
            <th>Max. Teilnehmer</th>
            <th>Teilnahmegebühr</th>
          </tr>
          </thead>
          <tbody>
            <tr>
              <td><?= $current_event->title ?></td>
              <td><?= $current_event->starts_at ?></td>
              <td><?= $current_event->ends_at ?></td>
              <td><?= $current_event->place ?></td>
              <td><?= $current_event->description ?></td>
              <td><?= $current_event->camping == 1 ? 'Zelten' : 'Hütte' ?></td>
              <td><?= $current_event->max_participants?></td>
              <td><?= formatted_fee_for($current_event)?></td>
            </tr>
          </tbody>
        </table>-->
<?php
}
}