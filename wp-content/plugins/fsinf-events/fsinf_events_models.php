<?php
function fsinf_events_config()
{
  return array(
    'events' => array(
      'title' => array(
        'type' => 'string',
        'max_length' => 127,
        'validation' => 'fsinf_validate_ne_string'
      ),
      'place' => array(
        'type' => 'string',
        'max_length' => 127,
        'validation' => 'fsinf_validate_ne_string'
      ),
      'starts_at' => array(
        'type' => 'string',
        'max_length' => 16,
        'validation' => 'fsinf_validate_date'
      ),
      'ends_at' => array(
        'type' => 'string',
        'max_length' => 16,
        'validation' => 'fsinf_validate_date'
      ),
      'description' => array(
        'type' => 'string'
      ),
      'camping' => array(
        'type' => 'int',
        'max_length' => 1,
        'default' => 0,
        'validation' => 'fsinf_validate_bool'
      ),
      'max_participants' => array(
        'type' => 'int',
        'max_value' => 127,
        'default' => 0
      ),
      'fee' => array(
        'type' => 'string',
        'validation' => 'fsinf_validate_money'
      )
    ),
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
        'default' => 0
      ),
      'tent_size' => array(
        'type' => 'int',
        'max_value' => 127,
        'default' => 0
      ),
      'notes' => array(
        'type' => 'string'
      )
    )
  );
}