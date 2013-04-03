<?php

if( !class_exists( 'umLocalizeScript' ) ) :
class umLocalizeScript {    
    
    function __construct(){
        add_action( 'wp_enqueue_scripts',           array( $this, 'runLocalization' ) );
        add_action( 'admin_enqueue_scripts',        array( $this, 'runLocalization' ) );         
    }
        
    function runLocalization(){
        global $userMeta; 
        
        $localizeText = array(
            'user-meta' => array(
                'get_pro_link'=> sprintf( __( 'Get pro version from %s to use this feature.', $userMeta->name ), $userMeta->website ),
                'please_wait'=> __( 'Please Wait...', $userMeta->name ),
            ),
            'fileuploader' => array(
                'upload'        => __( 'Upload', $userMeta->name ),
                'drop'          => __( 'Drop files here to upload', $userMeta->name ),
                'cancel'        => __( 'Cancel', $userMeta->name ),
                'failed'        => __( 'Failed', $userMeta->name ),
                'invalid_extension' => sprintf( __( '%1$s has invalid extension. Only %2$s are allowed.', $userMeta->name ), '{file}', '{extensions}' ),
                'too_large'         => sprintf( __( '%1$s is too large, maximum file size is %2$s.', $userMeta->name ), '{file}', '{sizeLimit}' ),
                'empty_file'        => sprintf( __( '%s is empty, please select files again without it.', $userMeta->name ), '{file}' ),
            ),
            'jquery.password_strength' => array(
                'too_weak'      => __( 'Too weak', $userMeta->name ),
                'weak'          => __( 'Weak password', $userMeta->name ),
                'normal'        => __( 'Normal strength', $userMeta->name ),
                'strong'        => __( 'Strong password', $userMeta->name ),
                'very_strong'   => __( 'Very strong password', $userMeta->name ),
            ),
            'validationEngine-en' => array(
                'required_field'    => __( '* This field is required', $userMeta->name ),
                'required_option'   => __( '* Please select an option', $userMeta->name ),
                'required_checkbox' => __( '* This checkbox is required', $userMeta->name ),
                'min'               => __( '* Minimum ', $userMeta->name ),
                'max'               => __( '* Maximum ', $userMeta->name ),
                'char_allowed'      => __( ' characters allowed', $userMeta->name ),
                'min_val'           => __( '* Minimum value is ', $userMeta->name ),
                'max_val'           => __( '* Maximum value is ', $userMeta->name ),
                'past'              => __( '* Date prior to ', $userMeta->name ),
                'future'            => __( '* Date past ', $userMeta->name ),
                'options_allowed'   => __( ' options allowed', $userMeta->name ),
                'please_select'     => __( '* Please select ', $userMeta->name ),
                'options'           => __( ' options', $userMeta->name ),
                'not_equals'        => __( '* Fields do not match', $userMeta->name ),
                'invalid_phone'     => __( '* Invalid phone number', $userMeta->name ),
                'invalid_email'     => __( '* Invalid email address', $userMeta->name ),
                'invalid_integer'   => __( '* Not a valid integer', $userMeta->name ),
                'invalid_number'    => __( '* Invalid floating decimal number', $userMeta->name ),
                'invalid_date'      => __( '* Invalid date, must be in YYYY-MM-DD format', $userMeta->name ),
                'invalid_time'      => __( '* Invalid time, must be in hh:mm:ss format', $userMeta->name ),
                'invalid_datetime'  => __( '* Invalid datetime, must be in YYYY-MM-DD hh:mm:ss format', $userMeta->name ),
                'invalid_ip'        => __( '* Invalid IP address', $userMeta->name ),
                'invalid_url'       => __( '* Invalid URL', $userMeta->name ),
                'numbers_only'      => __( '* Numbers only', $userMeta->name ),
                'letters_only'      => __( '* Letters only', $userMeta->name ),
                'no_special_char'   => __( '* No special characters allowed', $userMeta->name ),
                'user_exists'       => __( '* This user is already taken', $userMeta->name ),
            ),
       
        );
        
        foreach( $localizeText as $scriptName => $data ){
            $objectName = str_replace( array( '.', '-' ), '_', $scriptName );
            wp_localize_script( $scriptName, $objectName, $data );
        }
     
    }
              
}
endif;
