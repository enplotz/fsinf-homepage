<?php
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