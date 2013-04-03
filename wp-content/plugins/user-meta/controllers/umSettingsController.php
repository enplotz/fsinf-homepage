<?php

if( !class_exists( 'umSettingsController' ) ) :
class umSettingsController {
    
    function __construct() {
        //add_action('admin_menu',                    array( $this, 'admin_menu' )); 
        add_action( 'wp_ajax_um_update_settings',   array($this, 'ajaxUpdateSettings' ) );
    }

    function admin_menu(){
        global $userMeta;
        
        $page = add_submenu_page( 'usermeta', __( 'User Meta Settings', $userMeta->name ), __( 'Settings', $userMeta->name ), 'manage_options', 'user-meta-settings', array( $this, 'init' ));            
        
        $userMeta->addScript( 'jquery-ui-core',     'admin', $page );
        $userMeta->addScript( 'jquery-ui-widget',   'admin', $page );
        $userMeta->addScript( 'jquery-ui-mouse',    'admin', $page );
        $userMeta->addScript( 'jquery-ui-sortable', 'admin', $page );
        $userMeta->addScript( 'jquery-ui-draggable', 'admin', $page );
        $userMeta->addScript( 'jquery-ui-droppable', 'admin', $page );    
        $userMeta->addScript( 'jquery-ui-accordion', 'admin', $page );    
        $userMeta->addScript( 'jquery-ui-tabs', 'admin', $page );    
        
        $userMeta->addScript( 'jquery.ui.all.css', 'admin', $page, 'jqueryui' );   
        
        $userMeta->addScript( 'plugin-framework.js',  'admin', $page );
        $userMeta->addScript( 'plugin-framework.css', 'admin', $page );
        $userMeta->addScript( 'user-meta.js',         'admin', $page ); 
        $userMeta->addScript( 'user-meta.css',        'admin', $page );      
                   
        $userMeta->addScript( 'validationEngine-en.js', 'admin', $page, 'jquery' ); 
        $userMeta->addScript( 'validationEngine.js',    'admin', $page, 'jquery' );    
        $userMeta->addScript( 'validationEngine.css',   'admin', $page, 'jquery' );
        
        
         /*$userMeta->enqueueScripts( array( 
            'plugin-framework', 
            'user-meta',           
            'jquery-ui-all',
             
            'jquery-ui-core',
            'jquery-ui-widget',
            'jquery-ui-mouse',
            'jquery-ui-sortable',
            'jquery-ui-draggable',
            'jquery-ui-droppable',
            'jquery-ui-accordion',
            'jquery-ui-tabs',
             
            //'fileuploader',
            //'wysiwyg',
            //'jquery-ui-datepicker',
            //'jquery-ui-slider',
            //'timepicker',
            'validationEngine',
            //'password_strength',
        ) );                      
        $userMeta->runLocalization();    */ 
        
        
    }  
    
    function init(){
        global $userMeta;
        
        $settings   = $userMeta->getData( 'settings' );
        $forms      = $userMeta->getData( 'forms' );
        $fields     = $userMeta->getData( 'fields' );
        $default    = $userMeta->defaultSettingsArray();                        
        
        $userMeta->render("settingsPage", array(
            'settings'  => $settings,
            'forms'     => $forms,
            'fields'    => $fields,
            'default'   => $default,
        ));            
                 
    }
    
    function ajaxUpdateSettings(){
        global $userMeta;
        $userMeta->verifyNonce();
        
        if( @$_REQUEST['action_type'] == 'authorize_pro' )
            $userMeta->updateProAccountSettings( $_REQUEST );
        
        $settings = $userMeta->arrayRemoveEmptyValue( @$_REQUEST );
                               
        $extraFieldCount    = @$settings['backend_profile']['field_count'];
        $extraFields        = @$settings['backend_profile']['fields'];
        
        if( is_array( $extraFields ) ){
            foreach( $extraFields as $key => $val ){
                if( $key >= $extraFieldCount )
                    unset( $settings['backend_profile']['fields'][ $key ] );
            }                    
        }
        
        unset( $settings['action'] );
        unset( $settings['pf_nonce'] );
        unset( $settings['is_ajax'] );
        unset( $settings['backend_profile']['field_count'] );
         
        $settings = apply_filters( 'user_meta_pre_configuration_update', $settings, 'settings' );
        
        $userMeta->updateData( 'settings', $settings );
        
        echo $userMeta->showMessage( __( 'Settings Successfully Saved.', $userMeta->name ) );
        die();
    }
    
}
endif;
?>