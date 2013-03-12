<?php
function fsinf_all_events_page() {

  // Process requests
  if (array_key_exists('fsinf_remove_event', $_POST)){
    fsinf_remove_event();
  }
    $current_event = fsinf_get_current_event();
    ?>
    <h2>Alle Events</h2>
    <div class="row">
      <div class="span12">
<?php if(is_null($current_event)) { ?>
        <div class="alert alert-info">
          <a class="close" data-dismiss="alert">×</a>
          <p>Aktuell ist kein Event eingetragen.</p>
        </div>
<?php } else { ?>
        <h3>Aktuelles Event: <?= $current_event->title ?> <small>am <?php setlocale(LC_TIME, "de_DE"); echo strftime("%d. %b %G",strtotime(htmlspecialchars($current_event->starts_at)))?></h3>
      </div>
<?php } ?>
      <div class="span12">
<?php
?>
<?php $coming_events = fsinf_get_coming_events(5); ?>
        <h3>Kommende 5 Events</h3>

<?php
if(sizeOf($coming_events) == 0) : ?>
          <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            <p>Aktuell gibt es keine kommenden Events.</p>
          </div>
<?php else : ?>
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
          foreach ($coming_events as $event) :
?>
            <tr>
              <td>
                <form action="" method="post">
                  <button type="submit" class="btn btn-danger btn-small" title="Entfernen" value="<?=$event->id?>" name="fsinf_remove_event"><i class="icon-trash"></i></button>
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
<?php endif; ?>
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