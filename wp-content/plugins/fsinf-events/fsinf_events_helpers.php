<?php

function fsinf_get_registration_params()
{
  $config = fsinf_events_config();
  $params = array();
  foreach (array_keys($config['participants']) as $field) {
      if (array_key_exists($field, $_POST)) {
        $params[$field] = $_POST[$field];
      }
  }
  return $params;
}

function fsinf_alert_info($input_string)
{
  echo "<div class='alert alert-info span4'>
          $input_string
        </div>";
}

function formatted_fee_for($event)
{
  $fee_string = ($event->fee / 100) . ',' . ($event->fee % 100) . ($event->fee % 10);
  $fee = floatval($fee_string);
  setlocale(LC_MONETARY, 'de_DE');
  return money_format('%#3.2i', $fee);
}

function is_admitted($participant)
{
  return intval($participant->admitted);
}

// Function from Wordpress Source Code v. 3.4.2
function fsinf_is_email( $email) {
        // Test for the minimum length the email can be
        if ( strlen( $email ) < 3 ) return false;

        // Test for an @ character after the first position
        if ( strpos( $email, '@', 1 ) === false ) return false;

        // Split out the local and domain parts
        list( $local, $domain ) = explode( '@', $email, 2 );

        // LOCAL PART
        // Test for invalid characters
        if ( !preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local ) ) return false;

        // DOMAIN PART
        // Test for sequences of periods
        if ( preg_match( '/\.{2,}/', $domain ) ) return false;

        // Test for leading and trailing periods and whitespace
        if ( trim( $domain, " \t\n\r\0\x0B." ) !== $domain ) return false;

        // Split the domain into subs
        $subs = explode( '.', $domain );

        // Assume the domain will have at least two subs
        if ( 2 > count( $subs ) ) return false;

        // Loop through each sub
        foreach ( $subs as $sub ) {
                // Test for leading and trailing hyphens and whitespace
                if ( trim( $sub, " \t\n\r\0\x0B-" ) !== $sub ) return false;
                // Test for invalid characters
                if ( !preg_match('/^[a-z0-9-]+$/i', $sub ) ) return false;
        }
        return true;
}

function fsinf_field_contents($field_name, $errors)
{
  return empty($errors) || !array_key_exists($field_name, $_POST) ? '' : htmlspecialchars($_POST[$field_name]);
}

function fsinf_bank_account_information()
{
?>  <dl>
      <dt>Inhaber:</dt>
      <dd>hier ausgeben</dd>
    </dl>
<?php
}
