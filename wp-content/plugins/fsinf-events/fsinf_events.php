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

define('FSINF_EVENTS_TABLE', $wpdb->prefix . "fsinf_events");
define('FSINF_PARTICIPANTS_TABLE', $wpdb->prefix . "fsinf_participants");

// Hook for adding admin menus
add_action('admin_menu', 'fsinf_events_add_pages');

// Run install script on plugin activation
register_activation_hook(__FILE__,'fsinf_events_install');

// Add shortcode for latest event
add_shortcode('fsinf_current_event', 'fsfin_events_booking_form');


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
        'default' => false,
      ),
      'tent_size' => array(
        'type' => 'int',
        'max_value' => 127,
        'default' => false,
      ),
      'notes' => array(
        'type' => 'string'
      )
    )
  );
}


function fsinf_validate_email($address)
{
  $validated = is_email($address);
  $ok = $validated === false;
  return array($ok, $ok ? $validated : "Ungültige Mail-Adresse.");
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
      if (array_key_exists($field, $_POST) && is_string($_POST[$field])) {
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
  return $errors;
}

/*
    FS Inf Events booking form.
    Embed with shortcode: [fsinf_current_event]
 */

function fsfin_events_booking_form()
{
  // Get current event
  global $wpdb;
  $curr_event = $wpdb->get_row(sprintf(
    "SELECT id, title, place, starts_at, ends_at, description, camping
     FROM %s
     WHERE starts_at > NOW()
     ORDER BY starts_at ASC
     LIMIT 1",
    FSINF_EVENTS_TABLE
  ));

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
?>  <p>Erfolgreich angemeldet.</p>
<?php
    else:
      echo '<pre>'. print_r($errors, true) . '</pre>';
    endif;
  }

?>  <h2>Anmeldung zum Event: <?= htmlspecialchars($curr_event->title); ?></h2>
      <form method="POST" action="">
        <label>Vorname
          <input type="text" name="first_name" maxlength="255"/>
        </label>
        <label>Nachname
          <input type="text" name="last_name" maxlength="255"/>
        </label>
        <label>E-Mail-Adresse
          <input type="email" name="mail_address" maxlength="127"/>
        </label>
        <label>Handy-Nummer
          <input type="text" name="mobile_phone" maxlength="255"/>
        </label>
        <label>Semester
        <select name="semester" id="semester">
<?php
for($i = 1; $i <= 6; $i++):
?>                <option value="<?=$i?>"><?=$i?>. Semester</option>
<?php
endfor;
?>                <option value="99">Anderes Semester (>6)</option>

            </select>
          </label>
          <label>Bachelor
            <input type="radio" name="bachelor" value="1" checked/>
          </label>
          <label>Master
            <input type="radio" name="bachelor" value="0"/>
          </label>
          <label>Kannst du mit dem Auto kommen?
            <input type="checkbox" name="has_car" value="1"/>
          </label>
          <label>Wie viele Plätze im Auto? Inkl. Fahrer
            <input type="number" name="car_seats" value="1" min="1" max="127"/>
          </label>
<?php  if (intval($curr_event->camping)) :
?>
          <label>Kannst du mit dem Zelt kommen?
            <input type="checkbox" name="has_tent" value="1"/>
          </label>
          <label>Wie viele Plätze im Zelt?
            <input type="number" name="tent_size" value="1" min="1" max="127"/>
          </label>
<?php endif;
?>
          <label>Bemerkungen (Vegi o.ä.)
          <textarea name="notes" rows="4"></textarea>
          </label>

          <input type="submit" class="btn btn-primary" name="fsinf_events_register" value="Anmelden"/>
      </form>

<?php
  echo '<pre>'.print_r($_POST, true).'</pre>';
}




