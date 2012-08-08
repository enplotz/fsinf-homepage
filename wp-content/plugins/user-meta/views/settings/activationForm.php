<?php
global $userMeta;
// Expected $data, $fields

//$cache = $userMeta->getData( 'cache' );
$auth = $userMeta->getProAuth();

$html = null;
    $html .= "<form id=\"um_activation_form\" method=\"post\" onsubmit=\"umAuthorizePro(this); return false;\" >";
    $html .= "<h4>$userMeta->title " . __( "Version:", $userMeta->name ) . " $userMeta->version</h4>";
    $html .= "<div class='pf_divider'></div>"; 

    $html .= '<h4>'. __( 'Account Details', $userMeta->name ) .'</h4>';
    $html .= '<p>'. sprintf( __( 'Enter your email and password for %s to activate your pro version.', $userMeta->name ), make_clickable( $userMeta->website ) ) . '</p>';
    
    $html .= $userMeta->createInput( 'account_email', 'text', array(
        'id'            => 'account_email',
        'value'         => @$auth['email'],
        'before'        => '<strong>' . __( 'Email:', $userMeta->name ) . '</strong>',
        'class'         => 'validate[required,custom[email]]',
        'label_class'   => 'pf_label',
        'style'         => 'width:200px;'
    ) );    
    
    $html .= $userMeta->createInput( 'account_pass', 'password', array(
        'id'            => 'account_pass',
        'before'        => '<strong>' . __( 'Password:', $userMeta->name ) . '</strong>',
        'class'         => 'validate[required]',
    ) );   
    
    $html .= "<input type=\"hidden\" name=\"action_type\" value=\"authorize_pro\">";    

    $html .= $userMeta->createInput( "save_field", "submit", array(
        "value" => __( 'Save', $userMeta->name ),
        "id"    => "authorize_pro",
        "class" => "button-secondary",
    ) );
    
    
    if( @$auth['email'] ){
        $msg = @$auth['status'];
    }else{
        if( @$userMeta->isPro )
            $msg = __( 'Enter email and password to activate Pro version', $userMeta->name );
        else
            $msg = __( 'Enter email and password for upgrade to pro version', $userMeta->name );            
    }      
    
        
    $html .= "&nbsp;&nbsp;<strong class=\"pf_ajax_result\">($msg)</strong>";
    
    if( !$userMeta->isPro && $userMeta->isPro() )
        $html .= " <strong><a href='" . $userMeta->pluginUpdateUrl() . "'>". __( 'Click for upgrade to Pro', $userMeta->name ) ."</a></strong> ";
    
    $html .= "</form>";

    //$html .= "<div class='pf_divider'></div>"; 

    
            

echo $userMeta->metaBox( __( 'User Meta Pro Account Information', $userMeta->name ), $html );
?>