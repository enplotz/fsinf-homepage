<?php
function fsinf_add_event_page() {

    ?><h2>Erstelle ein neues Event</h2>

    <div class="row">
        <div class="alert alert-danger span4">Work in Progress here, folks</div>
    </div>

<?php
$errors = array();
  if (array_key_exists('fsinf_events_new', $_POST)) {
    $errors = fsinf_events_new();
    #echo var_dump($errors);
    if(count($errors) == 0):
      fsinf_print_success_message_new_event();
    else:
?>    <div class="alert alert-error">
        Das Formular enthält noch fehlerhafte Eingaben. Bitte korrigiere diese und schicke es erneut ab.
      </div>
<?php
    endif;
  }

?>
    <div class="row">
        <div class="span8">

            <form action="" class="form-horizontal" method="POST">
                <div class="row">
                    <div class="span4">
                        <?php
                            $field_name = 'title';
                        ?>
                        <div class="control-group <?= error_class($field_name, $errors) ?>">
                            <label class="control-label" for="<?=$field_name?>">Titel</label>
                            <div class="controls">
                                <input type="text" id="<?=$field_name?>" name="<?=$field_name?>" placeholder="z.B. Ersti-Hütte" value="<?= fsinf_field_contents($field_name, $errors) ?>">
                                <span class="help-inline"><?= @$errors[$field_name] ?></span>
                            </div>
                        </div>
                        <?php
                            $field_name = 'starts_at';
                        ?>
                        <div class="control-group <?= error_class($field_name, $errors) ?>">
                            <label class="control-label" for="<?=$field_name?>">Beginn</label>
                            <div class="controls">
                                <input type="text" name="<?=$field_name?>" id="<?=$field_name?>" placeholder="z.B. 24-12-2012 15:00" value="<?= fsinf_field_contents($field_name, $errors) ?>">
                                <span class="help-inline"><?= @$errors[$field_name] ?></span>
                            </div>
                        </div>
                        <?php
                            $field_name = 'ends_at';
                        ?>
                        <div class="control-group <?= error_class($field_name, $errors) ?>">
                            <label class="control-label" for="<?=$field_name?>">Ende</label>
                            <div class="controls">
                                <input type="text" name="<?=$field_name?>" id="<?=$field_name?>" placeholder="z.B. 31-12-2012 15:00" value="<?= fsinf_field_contents($field_name, $errors) ?>">
                                <span class="help-inline"><?= @$errors[$field_name] ?></span>
                            </div>
                        </div>
                        <?php
                            $field_name = 'place';
                        ?>
                        <div class="control-group <?= error_class($field_name, $errors) ?>">
                            <label class="control-label" for="<?=$field_name?>">Ort</label>
                            <div class="controls">
                                <input type="text" name="<?=$field_name?>" id="<?=$field_name?>" placeholder="z.B. Schweiz" value="<?= fsinf_field_contents($field_name, $errors) ?>">
                                <span class="help-inline"><?= @$errors[$field_name] ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="span4">
                        <?php
                            $field_name = 'camping';
                        ?>
                        <div class="control-group <?= error_class($field_name, $errors) ?>">
                            <label class="control-label" for="<?=$field_name?>">Art</label>
                            <div class="controls">
                                <select id="<?=$field_name?>" name="<?=$field_name?>">
                                    <?php
                                        $selected = array_key_exists($field_name, $errors) ? intval($_POST[$field_name]) : 0 ;
                                    ?>
                                    <option value="0" <?= $selected == 0 ? 'selected="selected"' : '' ?>>Hütte</option>
                                    <option value="1" <?= $selected == 1 ? 'selected="selected"' : '' ?>>Zelten</option>
                                </select>
                                <span class="help-inline"><?= @$errors[$field_name] ?></span>
                            </div>
                        </div>
                        <?php
                            $field_name = 'max_participants';
                        ?>
                        <div class="control-group <?= error_class($field_name, $errors) ?>">
                            <label class="control-label" for="<?=$field_name?>">Teilnehmer</label>
                            <div class="controls">
                                <input type="number" name="<?=$field_name?>" id="<?=$field_name?>" placeholder="z.B. 30" value="<?= fsinf_field_contents($field_name, $errors) ?>">
                                <span class="help-inline"><?= @$errors[$field_name] ?></span>
                            </div>
                        </div>
                        <?php
                            $field_name = 'fee';
                        ?>
                        <div class="control-group <?= error_class($field_name, $errors) ?>">
                            <label class="control-label" for="<?=$field_name?>">Gebühr</label>
                            <div class="controls">
                                <input type="number" name="<?=$field_name?>" min="0" step="0.01" size="4" id="<?=$field_name?>" placeholder="z.B. 10.05" value="<?= fsinf_field_contents($field_name, $errors) ?>">
                                <span class="help-inline"><?= @$errors[$field_name] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                            $field_name = 'description';
                        ?>
                <div class="control-group <?= error_class($field_name, $errors) ?>">
                    <label class="control-label" for="<?=$field_name?>">Kurz-Beschreibung</label>
                    <div class="controls">
                        <textarea id="<?=$field_name?>" name="<?=$field_name?>" placeholder="z.B. Mehr kann man dann im Artikel schreiben" rows="4" cols="40"><?= fsinf_field_contents($field_name, $errors) ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" name="fsinf_events_new" value="Create">Event erstellen</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php

}