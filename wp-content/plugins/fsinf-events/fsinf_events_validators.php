<?php
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