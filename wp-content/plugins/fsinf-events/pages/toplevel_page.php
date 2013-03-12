<?php
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

  if(is_null($current_event)) {
?>  <div class="alert alert-info">
      <a class="close" data-dismiss="alert">×</a>
      <p>Aktuell ist kein Event eingetragen.</p>
    </div>
<?php
  return;
  }
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
?>          <div class="row">
            <div class="span4">
<?php
            if ($number_seats_admitted >= $number_admitted_registrations):
?>            <p class="alert alert-success">Genug Sitzplätze</p>
<?php       else:
?>            <p class="alert alert-error">Nicht genug Sitzplätze</p>
<?php
            endif;
?>
            <ul>
              <li>Teilnahmegebühr: <?= formatted_fee_for($current_event); ?></li>
              <li>Maximale Teilnehmerzahl: <?= $current_event->max_participants ?></li>
              <li>Anzahl Teilnahmen insgesamt: <?= $number_registrations ?></li>
              <li>Anzahl Teilnahmen zugelassen: <?= $number_admitted_registrations ?></li>
              <li>Anzahl Sitzplätze insgesamt: <?= $number_seats ?></li>
              <li>Anzahl Sitzplätze zugelassen: <?= $number_seats_admitted ?></li>
            </ul>
          </div>
            <dl class="span4">
              <dt>Inhaber</dt><dd> <?= get_option( "konto_inhaber" ); ?></dd>
              <dt>Kontonummer</dt><dd><?= get_option( "konto_nummer" ); ?></dd>
              <dt>BLZ</dt><dd><?= get_option( "konto_bankleitzahl" ); ?></dd>
              <dt>Institut</dt><dd> <?= get_option( "konto_institut" ); ?></dd>
            </dl>
          </div> <!-- END row -->
<?php
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
<?php
  if ($current_event->camping):
?>
                  <th>Zelt</th>
<?php
  endif;
?>
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
                            <button type="submit" class="btn btn-mini" title="Hat bezahlt" value="<?= htmlspecialchars($participant->mail_address);?>" name="participation_paid_by"><i class="icon-shopping-cart"></i></button>
                          <?php
                            else:
                          ?>
                            <button type="submit" class="btn btn-mini" title="Hat nicht bezahlt" value="<?= htmlspecialchars($participant->mail_address);?>" name="participation_not_paid_by"><i class="icon-remove-sign"></i></button>
                          <?php
                            endif;
                          ?>
                          <?php
                            if (!$participant->admitted):
                          ?>
                            <button type="submit" class="btn btn-mini" title="Zugelassen" value="<?= htmlspecialchars($participant->mail_address);?>" name="participation_admitted_for"><i class="icon-ok"></i></button>
                          <?php
                            else:
                          ?>
                            <button type="submit" class="btn btn-mini" title="Nicht mehr zugelassen" value="<?= htmlspecialchars($participant->mail_address);?>" name="participation_not_admitted_for"><i class="icon-remove"></i></button>
                          <?php
                            endif;
                          ?>
                          <button type="submit" class="btn btn-danger btn-mini" title="Entfernen" value="<?= htmlspecialchars($participant->mail_address);?>" name="participation_cancel_for"><i class="icon-trash"></i></button>
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
<?php
  if ($current_event->camping):
?>
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
<?php
  endif;
?>
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
