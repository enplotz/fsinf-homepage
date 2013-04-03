<?php

if (!class_exists('umShortcodesController')) :
class umShortcodesController {
    
    function __construct(){
        global $userMeta;
        
        add_shortcode( 'user-meta', array( $this, 'init' ) );
                                     
        //add_action( 'wp_ajax_um_insert_user',                   array( $userMeta, 'ajaxInsertUser' ) );
        //add_action( 'wp_ajax_nopriv_um_insert_user',            array( $userMeta, 'ajaxInsertUser' ) );
        //add_action( 'wp_ajax_um_login',                         array( $userMeta, 'ajaxLogin' ) );
        //add_action( 'wp_ajax_nopriv_um_login',                  array( $userMeta, 'ajaxLogin' ) );          
        
        add_action( 'wp_ajax_um_show_uploaded_file',            array( $userMeta, 'ajaxShowUploadedFile' ) );
        add_action( 'wp_ajax_nopriv_um_show_uploaded_file',     array( $userMeta, 'ajaxShowUploadedFile' ) );
          
        add_action( 'wp_ajax_um_validate_unique_field',         array( $userMeta, 'ajaxValidateUniqueField' ) );
        add_action( 'wp_ajax_nopriv_um_validate_unique_field',  array( $userMeta, 'ajaxValidateUniqueField' ) );
        
        add_action( 'media_buttons_context',                    array( $this, 'addUmButton' ) );
        add_action( 'admin_footer',                             array( $this, 'shortcodeGeneratorPopup' ) );
    }


    function init( $atts ){
        global $userMeta;
        
        extract( shortcode_atts( array(
            'type'  => 'profile', // profile,registration,profile-registration,public
            'form'  => null,   		
            'diff'  => null,
    	), $atts ) );
                
        
        $actionType = strtolower( $type );
        
        // Replace "both" to "profile-registration" and "none" to "public"
        $actionType = str_replace( array( 'both', 'none' ), array( 'profile-registration', 'public' ), $actionType );
        
        if( $actionType == 'login' )
            return $userMeta->userLoginProcess( $form ); 
        else
            return $userMeta->userUpdateRegisterProcess( $actionType, $form, $diff ); 
        
        /*if( in_array( $actionType, array( 'registration', 'profile', 'profile-registration', 'public' ) ) )
            return $userMeta->userUpdateRegisterProcess( $actionType, $form, $diff ); 
        elseif( $actionType == 'login' )
            return $userMeta->userLoginProcess( $form );
        else
            return $userMeta->showError( sprintf( __( 'type="%s" is invalid.', $userMeta->name ), $actionType ) );*/           
    }   
    
    function addUmButton( $context ){
        global $userMeta, $pagenow;
        
        if( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) )
            return $context;

        $img = $userMeta->assetsUrl . 'images/ump-icon.png';

        $container_id = 'um_shortcode_popup';

        $title = __( 'Add User Meta Shortcode', $userMeta->name );

        $context .= "<a class='thickbox' title='{$title}'
        href='#TB_inline?width=600&height=600&inlineId={$container_id}'>
        <img src='{$img}' /></a>";

        return $context;        
    }
    
    function shortcodeGeneratorPopup(){
        global $userMeta, $pagenow;
        
        if( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) )
            return;
        
         $userMeta->enqueueScripts( array( 
            'plugin-framework', 
            'user-meta',           
        ) );                      
        $userMeta->runLocalization();         
        
        $actionTypes = $userMeta->validActionType(); 
        array_unshift( $actionTypes, null ); 
        
        $formsList = $userMeta->getFormsName();
        array_unshift( $formsList, null );       
        
        $userMeta->render( 'shortcodePopup', array(
            'actionTypes'   => $actionTypes,
            'formsList'     => $formsList,
            'roles'         => $userMeta->getRoleList(),
        ) );
    }
}
endif;
?>