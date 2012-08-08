<?php
global $userMeta, $user_ID;
// Expect by rander: $field, $actionType, $userID, $inPage, $inSection, $isNext, $isPrevious, $currentPage

// Initialiaze default value
$fieldType      = 'text';
$class          = 'um_input ';
$divClass       = null;
$divStyle       = null;
$inputStyle     = null;
$fieldOptions   = null;
$html           = null;
$validation     = null;
$maxlength      = null;
$by_key         = false;
$label_class    = null;
$fieldTitle     = null;
$fieldID        = "um_field_{$field['field_id']}";
$showInputField = true;


/***** Conditions *****/

if( isset( $field['admin_only'] ) ) :
    if( !$userMeta->isAdmin() )
        return;
endif;

if( isset( $field['read_only_non_admin'] ) ) :
    if( !($userMeta->isAdmin()) )
        $fieldReadOnly = 'disabled'; 
endif;
     
if( isset( $field['read_only'] ) )
    $fieldReadOnly = 'disabled';   
    
if( isset( $field['required'] ) )
    $validation .= 'required,';

if( isset( $field['unique'] ) ){
    //$validation .= "ajax[ajaxValidateUniqueField],";
    //$class .= "um_unique ";
}

if( isset( $field['title_position'] ) ){
    if( $field['title_position'] <> 'hidden' AND isset($field['field_title']) )
        $fieldTitle = $field['field_title'];
}

if( isset( $field['field_size'] ) ){
    $inputStyle = "width:{$field['field_size']} ";
}

if( isset( $field['field_height'] ) ){
    $inputStyle .= "height:{$field['field_height']}; ";
}

if( isset( $field['max_char'] ) ){
    $maxlength = $field['max_char'];
}

if( isset( $field['css_class'] ) ){
    $divClass .= "{$field['css_class']} ";
}

if( isset( $field['css_style'] ) ){
    $divStyle .= "{$field['css_style']} ";
}


if( isset( $field['options'] ) ){
    $temp = explode( ",", $field['options'] );
    foreach( $temp as $val ){
        $option     = explode( "=", $val );
        $optionKey  = trim($option[0]);
        $optionVal  = isset($option[1]) ? trim($option[1]) : $optionKey;
        $fieldOptions[$optionKey] = $optionVal;
    }
    $by_key = true;
}

 
/***** Fields Condition *****/    
    
// WP Default Fields       
if( $field['field_type'] == 'user_login' ) :
    if( $actionType <> 'registration' ):
        $fieldReadOnly = 'disabled';
    endif;        
    //$validation .= "required,ajax[ajaxValidateUniqueField],";
    $validation .= "required";
    
    
elseif( $field['field_type'] == 'user_email' ) :
    if( $field['field_type'] == 'user_email' )
        $validation .= "required,";
    $validation .= "custom[email],";
        
    //$validation .= "required,custom[email],ajax[ajaxValidateUniqueField],";
    if( isset( $field['retype_email'] ) ):
        $field2['class']        = $class . "validate[equals[$fieldID]]";
        $field2['fieldID']      = $fieldID . "_retype";   
        $field2['fieldTitle']   = "Retype " . $fieldTitle;     
    endif;
    
    
elseif( $field['field_type'] == 'user_pass' ) :
    $fieldType = 'password';  
    $field['field_value'] = null;
    
    if( $actionType == 'registration' )
        $validation .= 'required,';    
    
    if( isset($field['password_strength']) ) 
        if($field['password_strength']) $class .= "pass_strength "; 
        
    if( isset( $field['retype_password'] ) ):
        $field2['class']        = str_replace( "pass_strength", "", $class ) . "validate[equals[$fieldID]]";
        $field2['fieldID']      = $fieldID . "_retype";   
        $field2['fieldTitle']   = __( 'Retype ', $userMeta->name ) . $fieldTitle;     
    endif;        
           
 
elseif( $field['field_type'] == 'user_nicename' ):

elseif( $field['field_type'] == 'user_url' ):
    $validation .= "custom[url],";
    

elseif( $field['field_type'] == 'display_name' ):

elseif( $field['field_type'] == 'nickname' ):

elseif( $field['field_type'] == 'first_name' ):   

elseif( $field['field_type'] == 'last_name' ):  

elseif( $field['field_type'] == 'description' ) :
    $fieldType = 'textarea';
    if(isset($field['rich_text'])) :
        $class .= "um_rich_text ";
    endif;


elseif( $field['field_type'] == 'user_registered' ):  
    $validation .= "custom[datetime],";
    $class .= "um_datetime ";  


elseif( $field['field_type'] == 'role' ): 
    if( $user_ID && ($actionType != 'registration') )
        $field['field_value'] = $userMeta->getUserRole( $userID );
    $fieldType      = 'select';
    $by_key         = true;
    $fieldOptions   = $userMeta->getRoleList(); 

    
elseif( $field['field_type'] == 'jabber' ):  

elseif( $field['field_type'] == 'aim' ):  

elseif( $field['field_type'] == 'yim' ):   



// Standard Fields
elseif( $field['field_type'] == 'text' ):   
    
elseif( $field['field_type'] == 'textarea' ):   
    $fieldType = 'textarea';
    
    
elseif( $field['field_type'] == 'select' ): 
    $fieldType = 'select';


elseif( $field['field_type'] == 'checkbox' ):
    $fieldType              = 'checkbox';
    $field['field_value']   = $userMeta->toArray( $field['field_value'], ',' );
    $option_after           = "<br />";
    $combind                = true;
    if( isset( $field['required'] ) ) :
        $validation = 'minCheckbox[1],';  
    endif;  


elseif( $field['field_type'] == 'radio' ):   
    $fieldType      = 'radio';
    $option_after   = "<br />";


elseif( $field['field_type'] == 'password' ): 
    $fieldType            = 'password';
    $field['field_value'] = null;


elseif( $field['field_type'] == 'hidden' ):   
    $fieldType = 'hidden';


elseif( $field['field_type'] == 'user_avatar' ):
    
    $userAvatar = false;
    if( $field['field_type'] == 'user_avatar' ){
        if( @$field[ 'field_value' ] ){
            $userAvatar = get_avatar( $userID );
        }else{
            if( ! @$field['hide_default_avatar'] )
                $userAvatar = (@$actionType == 'registration') ? get_avatar( 'nobody@noemail' ) : get_avatar( $userID );
        }
    }
         
    $fieldResultContent = $userMeta->render( 'showFile', array(
        'filepath'  => $field['field_value'],
        'field_name' => $field['field_name'],
        'avatar'    => $userAvatar,
        'width'     => @$field['image_width'],
        'height'    => @$field['image_height'],
        'readonly'  => @$fieldReadOnly,
    ) );
    
    $fieldResultDiv = true;
    
    if( @$field['disable_ajax'] ){
        $fieldType  = 'file';
        $validation = str_replace( 'required,', '', $validation );
    }else{
        $showInputField = false;
        $extension = null; $maxsize = null;
        if( isset($field['allowed_extension']) )
            $extension = $field['allowed_extension'];  
        if( isset($field['max_file_size']) )
            $maxsize = $field['max_file_size'] * 1024;
        $html .= "$fieldTitle";
        if( !@$fieldReadOnly ):
            $html .= "<div id='$fieldID' name='{$field['field_name']}' class='um_file_uploader_field' extension='$extension' maxsize='$maxsize'></div>"; 
        endif;
    }
    

elseif( $field['field_type'] == 'rich_text' ):  
    $fieldType = 'textarea';
    $class    .= "um_rich_text ";




endif;



if( $validation ) $class .= "validate[" . rtrim( $validation, ',') . "]";

if($showInputField){
    $html .= $userMeta->createInput( $field['field_name'], $fieldType, array(
                "value"         => isset($field['field_value']) ? $field['field_value'] : null,
                "label"         => $fieldTitle,
                "disabled"      => isset($fieldReadOnly)    ? $fieldReadOnly : null,
                "id"            => $fieldID,
                "class"         => $class,
                "style"         => @$inputStyle             ? $inputStyle :null,
                "maxlength"     => $maxlength,
                "option_after"  => isset($option_after)  ? $option_after : null,
                "by_key"        => $by_key,
                "label_class"   => $label_class ? $label_class : 'pf_label',
                "onblur"        => isset($onBlur)           ? $onBlur : null,
                "combind"       => isset($combind)          ? $combind : false,
            ), $fieldOptions );    
}

if( isset($field2) ){
    extract($field2);    
    $html .= $userMeta->createInput( $field['field_name'], $fieldType, array(
                "value"         => isset($field['field_value']) ? $field['field_value'] : null,
                "label"         => $fieldTitle,
                "disabled"      => isset($fieldReadOnly) ? $fieldReadOnly : null,
                "id"            => $fieldID,
                "class"         => $class,
                "style"         => isset($inputStyle)       ? $inputStyle :null,
                "maxlength"     => $maxlength,
                "label_class"   => $label_class ? $label_class : 'pf_label',
            ) );      
}

if( isset( $field['description'] ) ){
    $html .= "<p class='um_description'>{$field['description']}</p>";
}

$fieldResultContent = isset($fieldResultContent) ? $fieldResultContent : null;
$fieldResultDiv = isset($fieldResultDiv) ? "<div id='{$fieldID}_result' class='um_field_result'>$fieldResultContent</div>" : null;
$moreContent = isset($moreContent) ? $moreContent : null;

$divStyle = $divStyle ? "style='$divStyle'" : null;
$html = "<div class='um_field_container $divClass' $divStyle>$html $fieldResultDiv $moreContent</div>";

?>