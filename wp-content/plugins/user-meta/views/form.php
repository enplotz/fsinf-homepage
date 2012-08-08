<?php
global $userMeta;
//need to set: $id, $form, $fields

$form_key   = isset($form['form_key'])    ? $form['form_key']     : null;

$html = null;

$html .= $userMeta->createInput( "forms[$id][form_key]", "text", array( 
    "value"     => $form_key,
    "label"     =>  __( 'Form Name',$userMeta->name ) . " <span class='um_required'>*</span>",
    "id"        => "um_form_$id",
    "class"     => "validate[required]",
    "label_class" => "pf_label",
    "onkeyup"   => "umChangeFormTitle(this)",
    "after"     => "<br />" . __( '(Give a name to your form)',$userMeta->name ),
    "enclose"   => "div class='um_left'",
     ) );   

//$html .= "<div class='um_left'>";
    //$html .= "<div class='um_left'>";
        //$html .= "<h4>Shortcode</h4>";
        //$html .= "<p>Shortcode</p>";
    //$html .= "<div>";
//$html .= "<div>";

$html .= "<div class='clear'></div>";
$html .= "<br /><br /><br />";

$html .= "<div class=\"um_left um_block_title\">" . __( 'Fields in your form (Drag from available fields)', $userMeta->name ) . "</div>";
$html .= "<div class=\"um_right um_block_title\">". __('Available Fields', $userMeta->name ) . "</div>";
$html .= "<div class='clear'></div>";

//Showing selected fields
$html .= "<div class='um_selected_fields um_left um_dropme'>";
if( isset( $form['fields'] ) ) {
    foreach( $form['fields'] as $fieldID ){
        if( isset( $fields[$fieldID] ) ){
            $fieldTitle = isset( $fields[$fieldID]['field_title'] ) ? $fields[$fieldID]['field_title'] : null;
            $html .= "<div class='button'>$fieldTitle ({$fields[$fieldID]['field_type']}) ID:$fieldID<input type='hidden' name='forms[$id][fields][]' value='$fieldID' /></div>";
            unset( $fields[$fieldID] );            
        }
    }    
}
$html .= "</div>";


$html .= "<div class='um_availabele_fields um_right um_dropme'>";
if( is_array( $fields ) ){
    foreach( $fields as $fieldID => $fieldData ){
        $fieldTitle = isset( $fieldData['field_title'] ) ? $fieldData['field_title'] : null;
        $html .= "<div class='button'>$fieldTitle ({$fieldData['field_type']}) ID:$fieldID<input type='hidden' name='forms[$id][fields][]' value='$fieldID' /></div>";    
    }
}
$html .= "</div>";

$html .= "<div class='clear'></div>";

$html .= "<div class=\"um_block_title\">" . __( 'Drag fields from right block to left block for make them available to your form.', $userMeta->name ) . "</div>";

  
$html .= $userMeta->createInput( "forms[$id][disable_ajax]", "checkbox", array( 
    "value"     => @$form['disable_ajax'],
    "label"     => __( 'Do not use AJAX submit', $userMeta->name ),
    "enclose"   => "p",
) );

$html .= "<input type='hidden' name='forms[$id][field_count]' id='field_count_$id' value='' />";


$metaBoxOpen = true;
if( isset($id) )
    if( !($id == 1) ) $metaBoxOpen = false;
    
$newFormText = __( 'New Form', $userMeta->name );
    
$metaBoxTitle = ($form_key) ? $form_key : $newFormText;
if( $metaBoxTitle == $newFormText ) $metaBoxOpen = true;

echo $userMeta->metaBox( $metaBoxTitle, $html, true, $metaBoxOpen );
?>
