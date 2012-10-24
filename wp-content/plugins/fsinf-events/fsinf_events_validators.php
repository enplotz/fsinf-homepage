<?php

// validate the model which has to be present in the config
function fsinf_validate_model($model){
  $validated = array();
  $errors = array();

  $config = fsinf_events_config();
  if (array_key_exists($model, $config)){
    foreach ($config[$model] as $field => $spec) {
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
  } else {
    $errors['config'] = "Model '{$model}' wurde bei der Validierung nicht in der Konfiguration gefunden.";
  }
  return array('validated' => $validated, 'errors' => $errors);
}

function fsinf_validate_email($address)
{
  $ne = fsinf_validate_ne_string($address);
  if(!$ne[0]) return $ne;
  $ok = fsinf_is_email($ne[1]);
  return array($ok, $ok ? $ne[1] : "Ungültige Mail-Adresse.");
}

// TODO crossvalidate start and end date
function fsinf_validate_date($date)
{
  $ne = fsinf_validate_ne_string($date);
  if(!$ne[0]) return $ne;
  $ok = fsinf_is_date($date);
  return array($ok, $ok ? $ne[1] : "Ungültiges Datumsformat.");
}

function fsinf_validate_money($money_string)
{
  $ne = fsinf_validate_ne_string($money_string);
  if(!$ne[0]) return $ne;
  $ok = fsinf_is_money($money_string);
  return array($ok, $ok ? $ne[1] : "Ungültiges Geldbetragsformat.");
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

function fsinf_is_date($datetime)
{
  // format: "dd-mm-yyyy hh:mm" (whitespace!)
  // ex: 31-12-2012 15:00
  if (preg_match("/[0-3]?[0-9]-[01]?[0-9]-\d{4}\s+([1-2][0-3]|[01]?[1-9]):[0-5][0-9]/", $datetime)){
    list($date, $time) = explode(' ', $datetime);
    list($d, $m, $y) = explode('-', $date, 3);
    return checkdate($m, $d, $y);

  }
  return false;
}

function fsinf_is_money($money)
{
  // 49.05 or 49.5 or 49; not: 1.234
  return preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $money);
}