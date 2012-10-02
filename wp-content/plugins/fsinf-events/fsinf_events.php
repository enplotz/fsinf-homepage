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
add_shortcode('fsinf_current_event', 'fsfin_events_booking_form');

// Add JS to Admin head
add_action('admin_head', 'fsinf_events_js');

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
                            camping TINYINT( 1 ) NOT NULL
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
    add_submenu_page('fsinf-events-top-level-handle', __('New Event','fsinf-events-new'), __('New Event','fsinf-events-new'), 'manage_options', 'fsinf-add-event-page', 'fsinf_add_event_page');

    // Add a second submenu to the custom top-level menu:
    add_submenu_page('fsinf-events-top-level-handle', __('All Events','fsinf-events-all'), __('All Events','fsinf-events-all'), 'manage_options', 'fsinf-all-events-page', 'fsinf_all_events_page');
}

// mt_toplevel_page() displays the page content for the custom FSInf-Events menu
function fsinf_events_toplevel_page() {
    echo "<h2>" . __( 'FSInf-Events', 'fsinf-events' ) . "</h2>";
?>
    <div id="fsinf-events-list">
      <?php
          $current_event = fsinf_get_current_event();
      ?>
            <h3>Aktuelles Event: <?= htmlspecialchars($current_event->title)?></h3>
            <?php if (!empty($_POST)) var_dump($_POST) ;?>
<?php

            $registrations = fsinf_get_registrations();
            #var_dump($registrations);
            if (count($registrations) > 0) {
?>
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>
                    Bearbeiten
                  </th>
                  <th>
                    Teilnehmer
                  </th>
                  <th>
                    E-Mail
                  </th>
                  <th>
                    Handy
                  </th>
                  <th>
                    Semester
                  </th>
                  <th>
                    Auto
                  </th>
                  <th>
                    Zelt
                  </th>
                  <th>
                    Zugelassen
                  </th>
                  <th>
                    Bezahlt
                  </th>
                  <th>
                    Anmerkungen
                  </th>
                </tr>
              </thead>
              <tbody>
<?php
              foreach ($registrations as $participant):
?>
                  <tr>
                    <td>
                        <form action="" method="post">
                          <input type="hidden" value="<?= $current_event->id;?>" name="event_id"/>
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
?>                  Ein Auto mit <?= htmlspecialchars($participant->has_car)?> <?= htmlspecialchars($participant->has_car) == 1 ? 'Sitz' : 'Sitzen'?>
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
?>                  Ein Zelt mit <?= htmlspecialchars($participant->has_tent)?> <?= htmlspecialchars($participant->has_tent) == 1 ? 'Schlafplatz' : 'Schlafplätzen'?>
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

<?php
}

function fsinf_get_registrations(){
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

// mt_sublevel_page2() displays the page content for the second submenu
// of the custom FSInf-Events menu
function fsinf_all_events_page() {
    echo "<h2>" . __( 'All Events', 'fsinf-events' ) . "</h2>";
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
    "SELECT id, title, place, starts_at, ends_at, description, camping
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
  echo '<pre>'.print_r($fields, true).'</pre>';
  return array();
}

function fsinf_bank_account_information()
{
?>  <h4>Kontodaten</h4>
    <dl>
      <dt>Inhaber:</dt>
      <dd>hier ausgeben</dd>
    </dl>
<?php
}

function fsinf_print_success_message(){
  $current_event = fsinf_get_current_event();
?>  <div class="alert alert-success alert-block">
      <a href="#" class="close" data-dismiss="alert">×</a>
      <h4>Erfolgreich angemeldet!</h4>
      <p>Du hast dich soeben erfolgreich für das Event
        <b><?=htmlspecialchars($current_event->title)?></b> angemeldet.</p>
        <p>Bitte Zahle die Teilnahmegebühr auf untenstehendes Konto ein.</p>
    </div>
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
    Embed with shortcode: [fsinf_current_event]
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