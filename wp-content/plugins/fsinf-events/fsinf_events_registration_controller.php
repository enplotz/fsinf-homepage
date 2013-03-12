<?php
function fsinf_events_register()
{
  $validation = fsinf_validate_model('participants');
  $errors = $validation['errors'];
  $validated = $validation['validated'];
  if (empty($errors)) {
    try {
      fsinf_save_registration($validated);
    } catch (Exception $exception) {
      var_dump($exception);
      return $exception;
    }
  } else {
    return $errors;
  }
}

# TODO: probably fix b/c it's very late
function send_registration_mail($fields){
  $current_event = fsinf_get_current_event();
  $fee = formatted_fee_for($current_event);
  # Array form of headers can set CC (e.g. to event admin)
  $headers = 'From: Fachschaft Informatik Uni Konstanz <fachschaft@inf.uni-konstanz.de>' . "\r\n";

  $topic = 'Anmeldung zum Event: ' .$current_event->title;

  $semester_string = $fields['semester'] <= 6 ? $fields['semester'].'.' : 'Höheres';
  $semester_string .= ' Semester im ';
  $semester_string .= $fields['bachelor'] == 1 ? 'Bachelor' : 'Master';

  $message = array();
  $message[] = "Yay! Du hast dich soeben erfolgreich zum Event ". $current_event->title . " am ".strftime('%d.%m.%Y',strtotime($current_event->starts_at))." angemeldet.";
  if($fields['semester'] <= 2):
    $message[] = "Bitte überweise $fee auf unten stehendes Konto.";
  else :
    $message[] = "Da Erstis bevorzugt werden, bist du noch nicht freigeschaltet. Wir setzen uns mit dir in Verbindung, wenn am Ende noch genug Plätze frei sein sollten.";
    $message[] = "Deshalb warte bitte noch mit dem Überweisen bis wir dich für die Teilnahme freigeschaltet haben.";
  endif;
  $message[] = "\n";
  $message[] = "==== Konto";
  $message[] = "Inhaber:\t" . get_option( "konto_inhaber" );
  $message[] = "Kontonummer:\t". get_option( "konto_nummer" );
  $message[] = "BLZ:\t\t". get_option( "konto_bankleitzahl" );
  $message[] = "Institut:\t". get_option( "konto_institut" );
  $message[] = "Betrag:\t\t". $fee;
  $message[] = "=============";
  $message[] = "\n";
  $message[] = "Deine Daten";
  $message[] = '------------';
  $message[] = "Name: " . $fields['first_name'].' '. $fields['last_name'];
  $message[] = "Handy-Nummer: " . $fields['mobile_phone'];
  $message[] = "Semester: " . $semester_string;

  if (array_key_exists('has_car', $fields)) :
    if ($fields['has_car'] == 1) :
      $car_string = 'Ein Auto mit ';
      $car_string .= $fields['car_seats'];
      $car_string .= $fields['car_seats'] == 1 ? ' Sitz' : ' Sitzen';
      $message[] = $car_string;
    endif;
  else:
    $message[] = 'Kein Auto';
  endif;

  if (array_key_exists('has_tent', $fields)) :
    if ($fields['has_tent'] == 1) :
      $tent_string = 'Ein Zelt mit ';
      $tent_string .= $fields['tent_size'];
      $tent_string .= $fields['tent_size'] == 1 ? ' Schlafplatz' : ' Schlafplätzen';
      $message[] = $tent_string;
    endif;
  else:
      $message[] = 'Kein Zelt';
  endif;
  if (array_key_exists('notes', $fields)) :
    $notes = $fields['notes'];
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
  $registration_data = fsinf_get_registration_params();
  $fee = formatted_fee_for($current_event);
?>  <div class="alert alert-success alert-block">
      <a href="#" class="close" data-dismiss="alert">×</a>
      <h4>Erfolgreich angemeldet!</h4>
      <p>Du hast dich soeben erfolgreich für das Event
        <b><?=htmlspecialchars($current_event->title)?></b> am <?= strftime('%d.%m.%Y',strtotime($current_event->starts_at)) ?> angemeldet.</p>
<?php if($registration_data['semester'] <= 2): ?>
        <p>Bitte zahle die Teilnahmegebühr von <b><?=$fee?></b> auf untenstehendes Konto ein.</p>
<?php else : ?>
    <p>Da Erstis bevorzugt werden, bist du noch nicht freigeschaltet. Wir setzen uns mit dir in Verbindung, wenn am Ende noch genug Plätze frei sein sollten.
       Deshalb warte bitte noch mit dem Überweisen bis wir dich für die Teilnahme freigeschaltet haben.
    </p>
<?php endif; ?>
    </div>
        <h4>Kontodaten</h4>
              <dt>Inhaber</dt><dd> <?= get_option( "konto_inhaber" ); ?></dd>
              <dt>Kontonummer</dt><dd><?= get_option( "konto_nummer" ); ?></dd>
              <dt>BLZ</dt><dd><?= get_option( "konto_bankleitzahl" ); ?></dd>
              <dt>Institut</dt><dd> <?= get_option( "konto_institut" ); ?></dd>
              <dt>Betrag</dt><dd><?=$fee?></dd>
        <h4>Deine Registrierungsinformationen</h4>
        <p>Folgende Informationen
        wurden dir auch an
        <b>
          <?= array_key_exists('mail_address',$_POST) ? htmlspecialchars($_POST['mail_address']) : 'keine E-Mail-Adresse angegeben?' ?>
        </b> gesendet:
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