
<?php 
global $userMeta;

$field_type_data    = $userMeta->getFields( 'key', $field_type );
$field_type_title   = $field_type_data['title'];
$field_group        = $field_type_data['field_group'];
$field_types_options = $userMeta->getFields( 'field_group', $field_group, 'title', !$userMeta->isPro );

if( $field_group == 'wp_default' )
    $field_title = isset($field_title) ? $field_title : $field_types_options[$field_type];


$fieldTitle = $userMeta->createInput( "fields[$id][field_title]", "text", array( 
    "value"         => isset($field_title) ? $field_title : null, 
    "label"         => __( 'Field Title', $userMeta->name ), 
    "id"            => "field_title_$id",
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "onkeyup"       => "umChangeFieldTitle(this)",
    //"after"     => "<div>(Title that will be shown on frontend)</div>",
    "enclose"       => "div class='um_segment'",
 ) );

$fieldTypes = $userMeta->createInput( "fields[$id][field_type]", "select", array( 
    "value"         => isset($field_type) ? $field_type : null,
    "label"         => __( 'Field Type', $userMeta->name ), 
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
    "onchange"      => "umChangeField(this, $id)",
    "by_key"        => true,
 ), $field_types_options ); 

$fieldDescription = $userMeta->createInput( "fields[$id][description]", "textarea", array( 
    "value"         => isset($description) ? $description : null,
    "label"         => __( 'Field Description', $userMeta->name ), 
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
 ) ); 
 
$fieldMetaKey = $userMeta->createInput( "fields[$id][meta_key]", "text", array( 
    "value"         => isset($meta_key) ? $meta_key : null,
    "label"         => __( 'Meta Key', $userMeta->name ), 
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "after"         => "<div style='margin-right:20px;'><span class='um_required'>Required Field.</span> Field data will save by metakey. Without defining metakey, field data will not be saved. e.g country_name (unique and no space)</div>",
    "enclose"       => "div class='um_segment'",
 ) );  
 
$fieldDefaultValue = $userMeta->createInput( "fields[$id][default_value]", "textarea", array( 
    "value"         => isset($default_value) ? $default_value : null,
    "label"         => __( 'Default Value', $userMeta->name ), 
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
 ) );
 
$fieldOptions = $userMeta->createInput( "fields[$id][options]", "textarea", array( 
    "value"         => isset($options) ? $options : null,
    "label"         => __( 'Field Options', $userMeta->name ),
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "after"         => "<div style='margin-right:20px;'><span class='um_required'>Required Field.</span> (e.g itm1, itm2) for Key Value: itm1=Item 1, itm2=Item 2</div>",
    "enclose"       => "div class='um_segment'",
 ) );  
 
$fieldRequired = $userMeta->createInput( "fields[$id][required]", "checkbox", array( 
    "value"     => isset($required) ? $required : null,
    "label"     => __( 'Required', $userMeta->name ) . "<br />",
 ) ); 
  
$fieldAdminOnly = $userMeta->createInput( "fields[$id][admin_only]", "checkbox", array( 
    "value"     => isset($admin_only) ? $admin_only : null,
    "label"     => __( 'Admin Only', $userMeta->name ) . "<br />",
 ) );     
 
$fieldReadOnly = $userMeta->createInput( "fields[$id][read_only]", "checkbox", array( 
    "value"     => isset($read_only) ? $read_only : null,
    "label"     => __( 'Read Only for all user', $userMeta->name ) . "<br />",
 ) ); 
 
$fieldReadOnly .= $userMeta->createInput( "fields[$id][read_only_non_admin]", "checkbox", array( 
    "value"     => isset($read_only_non_admin) ? $read_only_non_admin : null,
    "label"     => __( 'Read Only for non admin', $userMeta->name ) . "<br />",
 ) );   
 
$fieldUnique = $userMeta->createInput( "fields[$id][unique]", "checkbox", array( 
    "value"     => isset($unique) ? $unique : null,
    "label"     => __( 'Unique', $userMeta->name ) . "<br />",
 ) );  
 
$fieldNonAdminOnly = $userMeta->createInput( "fields[$id][non_admin_only]", "checkbox", array( 
    "value"     => isset($non_admin_only) ? $non_admin_only : null,
    "label"     => __( 'Non-Admin only', $userMeta->name ) . "<br />",
 ) );  
 
$fieldRegistrationOnly = $userMeta->createInput( "fields[$id][registration_only]", "checkbox", array( 
    "value"     => isset($registration_only) ? $registration_only : null,
    "label"     => __( 'Only on Registration Page', $userMeta->name ) . "<br />",
 ) );    
 
$fieldDisableAjax = $userMeta->createInput( "fields[$id][disable_ajax]", "checkbox", array( 
    "value"     => isset($disable_ajax) ? $disable_ajax : null,
    "label"     => __( 'Disable AJAX upload', $userMeta->name ) . "<br />",
) ); 
 
$fieldHideDefaultAvatar = $userMeta->createInput( "fields[$id][hide_default_avatar]", "checkbox", array( 
    "value"     => isset($hide_default_avatar) ? $hide_default_avatar : null,
    "label"     => __( 'Hide default avatar', $userMeta->name ) . "<br />",
) ); 

$fieldTitlePosition = $userMeta->createInput( "fields[$id][title_position]", "select", array( 
    "value"         => isset($title_position) ? $title_position : null,
    "label"         => __( 'Title Position', $userMeta->name ), 
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
    "by_key"        => true,
 ), array( 'top'=>'Top', 'hidden'=>'Hidden' ) );

$fieldCssClass = $userMeta->createInput( "fields[$id][css_class]", "text", array( 
    "value"         => isset($css_class) ? $css_class : null,
    "label"         => __( 'CSS Class', $userMeta->name ),  
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
 ) );
 
$fieldCssStyle = $userMeta->createInput( "fields[$id][css_style]", "textarea", array( 
    "value"         => isset($css_style) ? $css_style : null,
    "label"         => __( 'CSS Style', $userMeta->name ), 
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
 ) ); 
 
$fieldSize = $userMeta->createInput( "fields[$id][field_size]", "text", array( 
    "value"         => isset($field_size) ? $field_size : null,
    "label"         => __( 'Field Size', $userMeta->name ),  
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "after"         => "<div>(e.g. 200px;)</div>",
    "enclose"       => "div class='um_segment'",
 ) ); 
 
$fieldHeight = $userMeta->createInput( "fields[$id][field_height]", "text", array( 
    "value"         => isset($field_height) ? $field_height : null,
    "label"         => __( 'Field Height', $userMeta->name ), 
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "after"         => "<div>(e.g. 200px;)</div>",
    "enclose"       => "div class='um_segment'",
 ) );  
 
$fieldMaxChar = $userMeta->createInput( "fields[$id][max_char]", "text", array( 
    "value"         => isset($max_char) ? $max_char : null,
    "label"         => __( 'Max Char', $userMeta->name ), 
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
 ) );   
 

// For wp_default fields
$fieldForceUsername = $userMeta->createInput( "fields[$id][force_username]", "checkbox", array( 
    "value"     => isset($force_username) ? $force_username : null,
    "label"     => __( 'Force to change username', $userMeta->name ) . "<br />",
 ) ); 
$fieldRetypeEmail = $userMeta->createInput( "fields[$id][retype_email]", "checkbox", array( 
    "value"     => isset($retype_email) ? $retype_email : null,
    "label"     => __( 'Retype Email', $userMeta->name ) . "<br />",
 ) ); 
$fieldRetypePassword = $userMeta->createInput( "fields[$id][retype_password]", "checkbox", array( 
    "value"     => isset($retype_password) ? $retype_password : null,
    "label"     => __( 'Retype Password', $userMeta->name ) . "<br />",
 ) );  
$fieldPasswordStrength = $userMeta->createInput( "fields[$id][password_strength]", "checkbox", array( 
    "value"     => isset($password_strength) ? $password_strength : null,
    "label"     => __( 'Show password strength meter', $userMeta->name ) . "<br />",
 ) );   
 
$fieldShowDivider = $userMeta->createInput( "fields[$id][show_divider]", "checkbox", array( 
    "value"     => isset($show_divider) ? $show_divider : null,
    "label"     => __( 'Show Divider', $userMeta->name ) . "<br />",
 ) );     
 
$fieldRichText = $userMeta->createInput( "fields[$id][rich_text]", "checkbox", array( 
    "value"     => isset($rich_text) ? $rich_text : null,
    "label"     => __( 'Use Rich Text', $userMeta->name ) . "<br />",
 ) ); 
   
 
/* 
$fieldNameFormat = $userMeta->createInput( "fields[$id][name_format]", "select", array( 
    "value"         => isset($name_format) ? $name_format : null,
    "label"         => __( 'Name Format', $userMeta->name ), 
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
    "by_key"        => true,
 ), array( 'name'=>'Full Name', 'first_last'=>'First and Last Name', 'first_middle_last'=>'First, Middle and Last Name' ) ); 
 */
 
$fieldAllowedExtension = $userMeta->createInput( "fields[$id][allowed_extension]", "text", array( 
    "value"         => isset($allowed_extension) ? $allowed_extension : null,
    "label"         => __( 'Allowed Extension', $userMeta->name ), 
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "after"         => "<div>(default: jpg,png,gif)</div>",
    "enclose"       => "div class='um_segment'",
 ) );   

$fieldDateTimeSelection = $userMeta->createInput( "fields[$id][datetime_selection]", "select", array( 
    "value"         => isset($datetime_selection) ? $datetime_selection : null,
    "label"         => __( 'Type Selection', $userMeta->name ),
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
    "by_key"        => true,
 ), array( 'date'=>__('Date', $userMeta->name ), 'time'=>__('Time', $userMeta->name ), 'datetime'=>__( 'Date and Time', $userMeta->name ) ) ); 
 
$fieldCountrySelectionType = $userMeta->createInput( "fields[$id][country_selection_type]", "select", array( 
    "value"         => isset($country_selection_type) ? $country_selection_type : null,
    "label"         => __( 'Save meta value by', $userMeta->name ),
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
    "by_key"        => true,
 ), array( 'by_country_code'=>__('Country Code', $userMeta->name ), 'by_country_name'=>__('Country Name', $userMeta->name ) ) ); 

$fieldMaxNumber = $userMeta->createInput( "fields[$id][max_number]", "text", array( 
    "value"         => isset($max_number) ? $max_number : null,
    "label"         => __( 'Maximum Number', $userMeta->name ),
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
 ) );  
 
$fieldMinNumber = $userMeta->createInput( "fields[$id][min_number]", "text", array( 
    "value"         => isset($min_number) ? $min_number : null,
    "label"         => __( 'Minimum Number', $userMeta->name ),
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
 ) );  
 
$fieldMaxFileSize = $userMeta->createInput( "fields[$id][max_file_size]", "text", array( 
    "value"         => isset($max_file_size) ? $max_file_size : null, 
    "label"         => __( 'Max File Size', $userMeta->name ),
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "after"         => "<div>" . __( '(in KB. Default: 1024KB)', $userMeta->name ) . "</div>",
    "enclose"       => "div class='um_segment'",
 ) );
 
$fieldImageWidth = $userMeta->createInput( "fields[$id][image_width]", "text", array( 
    "value"         => isset($image_width) ? $image_width : null, 
    "label"         => __( 'Image Width (px)', $userMeta->name ),
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "after"         => "<div>" . __( '(For Image Only. e.g. 640)', $userMeta->name ) . "</div>",
    "enclose"       => "div class='um_segment'",
 ) );
 
$fieldImageHeight = $userMeta->createInput( "fields[$id][image_height]", "text", array( 
    "value"         => isset($image_height) ? $image_height : null, 
    "label"         => __( 'Image Height (px)', $userMeta->name ),
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "after"         => "<div>" . __( '(For Image Only. e.g. 480)', $userMeta->name ) . "</div>",
    "enclose"       => "div class='um_segment'",
 ) );  
 
$fieldCaptchaPublicKey = $userMeta->createInput( "fields[$id][captcha_public_key]", "text", array( 
    "value"         => isset($captcha_public_key) ? $captcha_public_key : null, 
    "label"         => __( 'reCaptcha Public Key', $userMeta->name ),
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
 ) );  
 
$fieldCaptchaPrivateKey = $userMeta->createInput( "fields[$id][captcha_private_key]", "text", array( 
    "value"         => isset($captcha_private_key) ? $captcha_private_key : null, 
    "label"         => __( 'reCaptcha Private Key', $userMeta->name ),
    "class"         => "um_input",
    "label_class"   => "pf_label",
    "enclose"       => "div class='um_segment'",
 ) );
 




$html  = "$fieldTitle $fieldTypes $fieldTitlePosition $fieldDescription";

//Single Field
if( $field_type == 'user_login' ):
    $html .= "$fieldSize";
    $html .= "<div class='um_segment'>$fieldAdminOnly</div>";
    $html .= "$fieldMaxChar $fieldCssClass $fieldCssStyle";  
    $html .= "<div class='um_segment'><p>" .  __( 'By default, <strong>Required</strong> and <strong>Unique</strong> validation will be set with this field. <strong>Read Only</strong> will be set conditionally.', $userMeta->name ) . "</p></div>";   

elseif( $field_type == 'user_email' ):
    $html .= "$fieldSize";
    $html .= "<div class='um_segment'>$fieldRetypeEmail $fieldAdminOnly $fieldReadOnly</div>";
    $html .= "$fieldMaxChar $fieldCssClass $fieldCssStyle";  
    $html .= "<div class='um_segment'><p>" .  __( 'By default, <strong>Required</strong> and <strong>Unique</strong> validation will be set with this field.', $userMeta->name ) . "</p></div>"; 

elseif( $field_type == 'user_pass' ):
    $html .= "$fieldSize";
    $html .= "<div class='um_segment'>$fieldRetypePassword $fieldPasswordStrength $fieldAdminOnly $fieldReadOnly</div>";
    $html .= "$fieldMaxChar $fieldCssClass $fieldCssStyle";  
    $html .= "<div class='um_segment'><p>" .  __( '<strong>Required</strong> validation will be set automatically when password field is being used by registration.', $userMeta->name ) . "</p></div>";  


//elseif( $field_type == 'user_nicename' ):    
//elseif( $field_type == 'user_url' ):
//elseif( $field_type == 'user_registered' ):
//elseif( $field_type == 'display_name' ):
//elseif( $field_type == 'first_name' OR $field_type == 'last_name' ):

elseif( $field_type == 'description' ):
    $html .= "$fieldSize";
    $html .= "<div class='um_segment'>$fieldRichText $fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldHeight $fieldMaxChar $fieldCssClass $fieldCssStyle"; 
    
elseif( $field_type == 'role' ):
    $html .= "$fieldDefaultValue";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldSize $fieldMaxChar $fieldCssClass $fieldCssStyle";     

elseif( $field_type == 'user_avatar' ): 
    $html .= "$fieldAllowedExtension"; 
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldDisableAjax $fieldHideDefaultAvatar</div>";  
    $html .= "$fieldMaxFileSize";
    $html .= "$fieldCssClass $fieldCssStyle"; 

    
    
    
elseif( $field_type == 'hidden' ):  
    $html .= "$fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldAdminOnly</div>";  
    $html .= "$fieldDefaultValue";    
        
elseif( $field_type == 'select' OR $field_type == 'checkbox' OR $field_type == 'radio' ):    
    $html .= "$fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue $fieldOptions";
    $html .= "$fieldSize $fieldCssClass $fieldCssStyle";
    
// Default property for fields. if no single settings are found
elseif( $field_group == 'wp_default' ):
    $html .= "$fieldSize";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldMaxChar $fieldCssClass $fieldCssStyle"; 

// Rendering Pro field
elseif( ( $field_group == 'standard' && !@$field_type_data['is_free'] ) || ( $field_group == 'formatting' ) ) :
    $html .= $userMeta->renderPro( 'fieldPro', array(
        'field_type'                => $field_type,
        'fieldMetaKey'              => $fieldMetaKey,
        'fieldRequired'             => $fieldRequired,
        'fieldAdminOnly'            => $fieldAdminOnly,
        'fieldReadOnly'             => $fieldReadOnly,
        'fieldUnique'               => $fieldUnique,
        'fieldDefaultValue'         => $fieldDefaultValue,
        'fieldSize'                 => $fieldSize,
        'fieldMaxChar'              => $fieldMaxChar,
        'fieldCssClass'             => $fieldCssClass,
        'fieldCssStyle'             => $fieldCssStyle,
        'fieldNonAdminOnly'         => $fieldNonAdminOnly,
        'fieldRegistrationOnly'     => $fieldRegistrationOnly,
        'fieldDisableAjax'          => $fieldDisableAjax,
        
        'fieldDateTimeSelection'    => $fieldDateTimeSelection,
        'fieldRetypePassword'       => $fieldRetypePassword,
        'fieldPasswordStrength'     => $fieldPasswordStrength,
        'fieldRetypeEmail'          => $fieldRetypeEmail,
        'fieldAllowedExtension'     => $fieldAllowedExtension,
        'fieldImageWidth'           => $fieldImageWidth,
        'fieldImageHeight'          => $fieldImageHeight,
        'fieldMaxFileSize'          => $fieldMaxFileSize,
        'fieldMinNumber'            => $fieldMinNumber,
        'fieldMaxNumber'            => $fieldMaxNumber,
        'fieldCountrySelectionType' => $fieldCountrySelectionType,
        'fieldShowDivider'          => $fieldShowDivider,
    ) );
     

elseif( $field_group == 'standard' ):
    $html .= "$fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue";
    $html .= "$fieldSize $fieldHeight $fieldMaxChar $fieldCssClass $fieldCssStyle";     
    
endif;    





$html = "<div id='field_$id'>$html</div>";  

$field_title = isset($field_title) ? $field_title : __( 'New Field', $userMeta->name );
$metaBoxTitle = "<span class='um_admin_field_title'>$field_title</span> (<span class='um_admin_field_type'>$field_type_title</span>) ID:$id";

$metaBoxOpen = true;
if( isset($n) )
    if( !($n == 1) ) $metaBoxOpen = false;

echo $userMeta->metaBox( $metaBoxTitle, $html, true, $metaBoxOpen );

?>