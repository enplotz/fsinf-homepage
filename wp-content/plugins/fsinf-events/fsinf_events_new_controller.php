<?php
  // Controller for validating a new event and printing messages

function fsinf_events_new()
{
  $validation = fsinf_validate_model('events');
  $errors = $validation['errors'];
  $validated = $validation['validated'];
  if (empty($errors)) {
    try {
      fsinf_save_event($validated);
    } catch (Exception $exception) {
      var_dump($exception);
      return $exception;
    }
  } else {
    return $errors;
  }
}

function fsinf_print_success_message_new_event()
{
  ?><div class="alert alert-success alert-block">
      Yay.
    </div>
  <?php
}