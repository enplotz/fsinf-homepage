<?php /*
Plugin Name: Stammtisch-Tool
Plugin URI: http://fachschaft.inf.uni-konstanz.de/
Description: Allows for easy booking of a regulars table.
Author: Manuel Hotz
Version: 0.1
Author URI: http://enplotz.de
*/

class Stammtisch_Tool extends WP_Widget {

 function Stammtisch_Tool() {
   $widget_ops = array(
              'classname' => 'stammtischtool',
              'description' => 'Specify a regulars table meeting.');

   $control_ops = array(
              'width' => 250,
              'height' => 500,
              'id_base' => 'stammtischtool-widget');

   $this->WP_Widget('stammtischtool-widget', 'Stammtisch-Tool', $widget_ops, $control_ops );
 }

function form ($instance) {

  $defaults = array('minparticipants' => '3','regulars_date'=>'Tuesday, 20:00','title'=>'Stammtisch', 'place'=>'', 'place_url'=>'');
  $instance = wp_parse_args( (array) $instance, $defaults ); ?>

  <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
      <input type="text" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?> " value="<?php echo $instance['title'] ?>" size="20">
  </p>

    <p>
      <label for="<?php echo $this->get_field_id('regulars_date'); ?>">Date (Day, Time):</label>
      <input type="text" name="<?php echo $this->get_field_name('regulars_date') ?>" id="<?php echo $this->get_field_id('regulars_date') ?> " value="<?php echo $instance['regulars_date'] ?>" size="20">
  </p>
  
    <p>
      <label for='<?php echo $this->get_field_id('minparticipants'); ?>'>Min. required No. of Participants:</label>
      <select id="<?php echo $this->get_field_id('minparticipants'); ?>" name="<?php echo $this->get_field_name('minparticipants'); ?>">
        <?php for ($i=1;$i<=20;$i++) {
          echo '<option value="'.$i.'"';
          if ($i==$instance['minparticipants']) echo ' selected="selected"';
          echo '>'.$i.'</option>';
        } ?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('place'); ?>">Place:</label>
      <input type="text" name="<?php echo $this->get_field_name('place') ?>" id="<?php echo $this->get_field_id('place') ?> " value="<?php echo $instance['place'] ?>" size="20">
  </p>

  <p>
      <label for="<?php echo $this->get_field_id('place_url'); ?>">Place URL:</label>
      <input type="text" name="<?php echo $this->get_field_name('place_url') ?>" id="<?php echo $this->get_field_id('place_url') ?> " value="<?php echo $instance['place_url'] ?>" size="20">
  </p>

  <?php
}

function update ($new_instance, $old_instance) {
  $instance = $old_instance;

  $instance['title'] = $new_instance['title'];
  $instance['regulars_date'] = $new_instance['regulars_date'];
  $instance['minparticipants'] = $new_instance['minparticipants'];
  $instance['place'] = $new_instance['place'];
  $instance['place_url'] = $new_instance['place_url'];

  return $instance;
}
function widget ($args,$instance) {
  extract($args);

  $title = $instance['title'];
  $regulars_date = $instance['regulars_date'];
  $minparticipants = $instance['minparticipants'];
  $place = $instance['place'];
  $place_url = $instance['place_url'];

  // retrieve posts information from database
  global $wpdb;
  //$posts = get_posts('numberposts='.$numberposts.'&category='.$catid);
  
  $out = '<ul>';  
  $out .= '<li>(# of '.$minparticipants.' needed)</li>';
  $out .= '<li>Next Meeting: '.$regulars_date.'</li>';
  $out .= '<li>Place: <a href="'.$place_url.'">'.$place.'</a></li>';
  $out .= '</ul>';

  //print the widget for the sidebar
  echo $before_widget;
  echo $before_title.$title.$after_title;
  echo $out;
  echo $after_widget;
}

}

function stammtischtool_load_widgets() {
  register_widget('Stammtisch_Tool');
}


add_action('widgets_init', 'stammtischtool_load_widgets');

?>