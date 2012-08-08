<?php
global $userMeta;

$html = null;

$html .= $userMeta->createInput( 'name', 'text', array(
    'label'         => __( 'From E-mail', $userMeta->name ),
    'value'         => null,
    'label_class'   => 'pf_label',
    'style'         => 'width:500px;',
    'enclose'       => 'p',
) );

$html .= $userMeta->createInput( 'name', 'text', array(
    'label'         => __( 'From Name', $userMeta->name ),
    'value'         => null,
    'label_class'   => 'pf_label',
    'style'         => 'width:500px;',
    'enclose'       => 'p',
) );

$emaiFormat = array(  
    'text/plain'    => __( 'Plain Text', $userMeta->name ), 
    'text/html'     => __( 'HTML', $userMeta->name ),
);

$html .= $userMeta->createInput("general[mail_content_type]", "select", array(
    'label'         => __('E-mail Format', $userMeta->name ),
    'value'         => null,
    'label_class'   => 'pf_label',
    'enclose'       => 'p',
    'by_key'        => true,
), $emaiFormat );

$html .= $userMeta->createInput( 'name', 'text', array(
    'label'         => __( 'Subject', $userMeta->name ),
    'value'         => null,
    'label_class'   => 'pf_label',
    'style'         => 'width:700px;',
    'enclose'       => 'p',
) );

$html .= $userMeta->createInput( 'name', 'textarea', array(
    'label'         => __( 'Body', $userMeta->name ),
    'value'         => null,
    'label_class'   => 'pf_label',
    'style'         => 'width:700px;height:300px;',
    'enclose'       => 'p',
) );

$html .= $userMeta->createInput( 'name', 'checkbox', array(
    'label'         => __( 'Disable Notification', $userMeta->name ),
    'value'         => null,
    'enclose'       => 'p',
) );

?>