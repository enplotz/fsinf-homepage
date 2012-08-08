<?php

if (!class_exists('umShortcodesController')) :
class umShortcodesController {
    
    function __construct(){
        global $userMeta;
        
        add_shortcode( 'user-meta', array( $this, 'init' ) );
        
        // Shortcode for backword version: 1.0.3 suppoert
        add_shortcode( 'user-meta-profile', array( $this, 'backwordInit' ) );                       
        
        add_action( 'wp_ajax_um_insert_user',                   array( $userMeta, 'ajaxInsertUser' ) );
        add_action( 'wp_ajax_nopriv_um_insert_user',            array( $userMeta, 'ajaxInsertUser' ) );
        
        add_action( 'wp_ajax_um_show_uploaded_file',            array( $userMeta, 'ajaxShowUploadedFile' ) );
        add_action( 'wp_ajax_nopriv_um_show_uploaded_file',     array( $userMeta, 'ajaxShowUploadedFile' ) );
          
        add_action( 'wp_ajax_um_validate_unique_field',         array( $userMeta, 'ajaxValidateUniqueField' ) );
        add_action( 'wp_ajax_nopriv_um_validate_unique_field',  array( $userMeta, 'ajaxValidateUniqueField' ) );  
                   
        add_action( 'wp_ajax_um_login',                         array( $userMeta, 'ajaxLogin' ) );
        add_action( 'wp_ajax_nopriv_um_login',                  array( $userMeta, 'ajaxLogin' ) );               
                

        $userMeta->addScript( 'jquery',             'shortcode', 'user-meta' );
        $userMeta->addScript( 'jquery-ui-core',     'shortcode', 'user-meta' );
        $userMeta->addScript( 'jquery-ui-tabs',     'shortcode', 'user-meta' );
        //$userMeta->addScript( 'jquery-ui-widget',   'front' );
        //$userMeta->addScript( 'jquery-ui-mouse',   'front' );

        
        $userMeta->addScript( 'jquery.ui.widget.js',            'shortcode', 'user-meta', 'jqueryui' );
        $userMeta->addScript( 'jquery.ui.mouse.js',             'shortcode', 'user-meta', 'jqueryui' );
        $userMeta->addScript( 'jquery.ui.slider.js',            'shortcode', 'user-meta', 'jqueryui' );
        $userMeta->addScript( 'jquery.ui.datepicker.js',        'shortcode', 'user-meta', 'jqueryui' );
        $userMeta->addScript( 'jquery-ui-timepicker-addon.js',  'shortcode', 'user-meta', 'jqueryui' );            
        $userMeta->addScript( 'jquery.ui.all.css',              'shortcode', 'user-meta', 'jqueryui' );
        
        $userMeta->addScript( 'jquery.wysiwyg.js',              'shortcode', 'user-meta', 'jquery' );
        $userMeta->addScript( 'wysiwyg.image.js',               'shortcode', 'user-meta', 'jquery' );
        $userMeta->addScript( 'wysiwyg.link.js',                'shortcode', 'user-meta', 'jquery' );
        $userMeta->addScript( 'wysiwyg.table.js',               'shortcode', 'user-meta', 'jquery' );
        $userMeta->addScript( 'jquery.wysiwyg.css',             'shortcode', 'user-meta', 'jquery' );
        $userMeta->addScript( 'jquery.tools.min.js',            'shortcode', 'user-meta', 'jquery' );                        

        $userMeta->addScript( 'validationEngine-en.js',         'shortcode', 'user-meta', 'jquery' );
        $userMeta->addScript( 'validationEngine.js',            'shortcode', 'user-meta', 'jquery' );   
        $userMeta->addScript( 'validationEngine.css',           'shortcode', 'user-meta', 'jquery' );
        //$userMeta->addScript( 'jquery.validate.js',             'shortcode', 'user-meta', 'jquery' );           
        $userMeta->addScript( 'jquery.password_strength.js',    'shortcode', 'user-meta', 'jquery' );
                                                       
        $userMeta->addScript( 'fileuploader.js',                'shortcode', 'user-meta', 'jquery' );
        $userMeta->addScript( 'fileuploader.css',               'shortcode', 'user-meta', 'jquery' );                        
                                           
        $userMeta->addScript( 'plugin-framework.js',    'shortcode', 'user-meta' );
        $userMeta->addScript( 'plugin-framework.css',   'shortcode', 'user-meta' );                        
        $userMeta->addScript( 'user-meta.js',           'shortcode', 'user-meta' );
        $userMeta->addScript( 'user-meta.css',          'shortcode', 'user-meta' );
       
    }
    
   
    function init( $atts ){     
        global $userMeta;
        
        // Using, when ajax faild
        $output = null;
        if( @$_REQUEST['action_type'] == 'profile' || @$_REQUEST['action_type'] == 'registration' )
            $output = $userMeta->ajaxInsertUser();
        elseif( $userMeta->isLoginRequest() )
            $output = $userMeta->ajaxLogin();
        
    	extract( shortcode_atts( array(
    		'form' => 'profile',
    		'type' => 'profile', // profile,registration,both,none
    	), $atts ) );
        
        $output .= $this->generateForm( $form, $type );   
        return $output;
    }
    
    
    function backwordInit(){
        return $this->generateForm( 'profile', 'profile' );
    }
    
    
    function generateForm( $formName, $actionType ){
        global $userMeta, $user_ID;
        
        if( !$userMeta->isValidFormType( $actionType ) )
            return $userMeta->showError( sprintf( __( 'Sorry. type="%s" is not supported.', $userMeta->name ), $actionType ), false );              

        if( ! (  $userMeta->isPro() && $userMeta->isPro ) ){
            if( !($actionType == 'profile' || $actionType == 'none') )
                return $userMeta->showError( "type='$actionType' is only supported, in pro version. Get " . $userMeta->getProLink( 'User Meta Pro' ), "info", false );                                    
        }
          
        if( @$actionType == 'login' )                       
            return $userMeta->generateLoginForm();
                   
        $userID  = $user_ID;
        $isAdmin = $userMeta->isAdmin();
        
        // Determine Form type
        if( $actionType == 'both' )
            $actionType = $user_ID ? 'profile' : 'registration';        
            
        // Checking Permission
        if( $actionType == 'profile' OR $actionType == 'none' ){
            if( !$user_ID )
                return $userMeta->showMessage( __( 'You do not have permission to access this page.', $userMeta->name ), 'info', false );
        }elseif( $actionType == 'registration' ) {
            if( $user_ID AND !$isAdmin )
                return $userMeta->showMessage( sprintf( __( 'You are already registered. See your <a href="%s">profile</a>', $userMeta->name ), $userMeta->getProfileLink() ) , 'info' );
            elseif( !get_option( 'users_can_register' ) )
                return $userMeta->showError( __( 'User registration is currently not allowed.', $userMeta->name ), false);            
        }
        
        
        // Loading $userID as admin request
        if( $isAdmin ){
            if( isset($_REQUEST['user_id']) )
                $userID = $_REQUEST['user_id'];
        }            
        
        $fields     = $userMeta->getData( 'fields' );
        $forms      = $userMeta->getData( 'forms' );                     
        $form       = isset( $forms[$formName] ) ? $forms[$formName] : null;
                    
        $userData   = get_userdata( $userID );
                   
        return $userMeta->renderPro( 'generateForm', array( 
            'actionType'=> $actionType,
            'fields'    => $fields, 
            'form'      => $form, 
            'userID'    => $userID,
            'userData'  => $userData,
        ) );
                   
    }

    
}
endif;
?>