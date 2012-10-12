<?php
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