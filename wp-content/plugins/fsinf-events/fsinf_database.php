<?php

/*
	File for Database Access Stuff
 */


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
                            fee MEDIUMINT UNSIGNED NOT NULL DEFAULT 0
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
    $wpdb->prepare(
      "SELECT  mail_address, event_id, first_name, last_name, mobile_phone, semester, bachelor, has_car, has_tent, car_seats, tent_size, notes, admitted, paid
       FROM  ".FSINF_PARTICIPANTS_TABLE."
       WHERE  event_id = %d
      ", $current_event->id
    )
    );
       #ORDER BY semester ASC
  return $results;
}

function fsinf_get_coming_events($count)
{
  global $wpdb;
  return $wpdb->get_results($wpdb->prepare(
    "SELECT id, title, place, starts_at, ends_at, description, camping, max_participants, fee
     FROM ".FSINF_EVENTS_TABLE."
     WHERE starts_at > NOW()
     ORDER BY starts_at ASC
     LIMIT %d",
     $count
  ));
}
function fsinf_get_past_events($count)
{
  global $wpdb;
  return $wpdb->get_results($wpdb->prepare(
    "SELECT id, title, place, starts_at, ends_at, description, camping, max_participants, fee
     FROM ".FSINF_EVENTS_TABLE."
     WHERE starts_at < NOW()
     ORDER BY ends_at DESC
     LIMIT %d", $count
  ));
}

function fsinf_remove_event()
{
  if (is_admin()){
    if (array_key_exists('fsinf_remove_event', $_POST)){
      global $wpdb;
      $ok = $wpdb->query($wpdb->prepare(
        "DELETE FROM ".FSINF_EVENTS_TABLE."
         WHERE id = %d
        ", $_POST['fsinf_remove_event']
        ));
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

  # Magic Number 1062 is for duplicate entry for key :/
  if (mysql_errno() == 1062) {
    throw new Exception("Eintrag existiert bereits", "1062");
  }
    send_registration_mail($fields);
    return array(); #TODO ???
}


function fsinf_save_event($fields)
{
  global $wpdb;
  $config = fsinf_events_config();
  $cfg = $config['events'];
  $types = array();
  foreach (array_keys($fields) as $name) {
    $types[$name] = $cfg[$name]['type'] === 'int' ? '%d' : '%s';
  }

  $fields['fee'] = $fields['fee'] * 100;

  // from: 31-12-2012 10:30
  // to:   2012-12-31 10:30:00
  $fields['starts_at'] = date_format(date_create_from_format('d-m-Y H:i', $fields['starts_at']), 'Y-m-d H:i:s');
  $fields['ends_at'] = date_format(date_create_from_format('d-m-Y H:i', $fields['ends_at']), 'Y-m-d H:i:s');

  #echo '<pre>'.print_r($fields, true).'</pre>';
  $result = $wpdb->insert(FSINF_EVENTS_TABLE,
          $fields,
          $types
    );

}


