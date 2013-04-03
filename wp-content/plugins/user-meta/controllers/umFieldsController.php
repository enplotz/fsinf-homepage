<?php

if( !class_exists( 'umFieldsController' ) ) :
class umFieldsController {
    
    function __construct(){      
        //add_action('admin_menu',                array( $this, 'menuItem' ) );    
        
        add_action( 'wp_ajax_um_add_field',     array($this, 'ajaxAddField' ) ); 
        add_action( 'wp_ajax_um_change_field',  array($this, 'akaxChangeField' ) ); 
        add_action( 'wp_ajax_um_update_field',  array($this, 'ajaxUpdateField' ) );                
    }
    
    function menuItem(){
        global $userMeta;

        $page = add_utility_page( 'User Meta', 'User Meta', 'manage_options', 'usermeta', array( $this, 'init' ), $userMeta->assetsUrl . 'images/ump-icon.png' ); 
        $page = add_submenu_page( 'usermeta', __( 'User Meta Fields Editor', $userMeta->name ), __( 'Fields Editor', $userMeta->name ), 'manage_options', 'usermeta', array( $this, 'init' ));     
               
        $userMeta->addScript( 'jquery-ui-core',     'admin', $page );
        $userMeta->addScript( 'jquery-ui-widget',   'admin', $page );
        $userMeta->addScript( 'jquery-ui-mouse',    'admin', $page );
        $userMeta->addScript( 'jquery-ui-sortable', 'admin', $page );
        $userMeta->addScript( 'jquery-ui-draggable','admin', $page );
        $userMeta->addScript( 'jquery-ui-droppable','admin', $page );
        
        $userMeta->addScript( 'plugin-framework.js',  'admin', $page );
        $userMeta->addScript( 'plugin-framework.css', 'admin', $page );
        $userMeta->addScript( 'user-meta.js',         'admin', $page ); 
        $userMeta->addScript( 'user-meta.css',        'admin', $page );      
        
        $userMeta->addScript( 'validationEngine-en.js', 'admin', $page, 'jquery' ); 
        $userMeta->addScript( 'validationEngine.js',    'admin', $page, 'jquery' ); 
        $userMeta->addScript( 'validationEngine.css',   'admin', $page, 'jquery' );           
    }
              
                          
    function init(){   
        global $userMeta;        
        
        $fields = $userMeta->getData( 'fields' );        
        $userMeta->render( 'fieldsEditorPage', array( 'fields'=>$fields ) );
    }
    
    
    function ajaxAddField(){
        global $userMeta;
        $userMeta->verifyNonce();
                  
        if( isset( $_REQUEST['field_type'] ) ){
            unset( $_REQUEST['action'] );
            $userMeta->render( 'field', $_REQUEST );
        }
        
        die();
    }
    
    
    function akaxChangeField(){
        global $userMeta;
        $userMeta->verifyNonce();
        
        if( !isset( $_POST['fields'] ) ) return;
        
        $data       =  $_POST['fields'] ;
        $fieldID    = key( $data );
                   
        $fieldData       = $data[$fieldID];
        $fieldData['id'] = $fieldID;          
        
        $userMeta->render( 'field', $fieldData );
        
        die();            
    }
    
    
    function ajaxUpdateField( ){
        global $userMeta;                        
        $userMeta->verifyNonce();            
             
        $data = array();
        if( isset( $_POST['fields'] ) )
            $data = $userMeta->arrayRemoveEmptyValue( $_POST['fields'] );
 
        $data = apply_filters( 'user_meta_pre_configuration_update', $data, 'fields_editor' );
        
        $userMeta->updateData( 'fields', $data );
        
        echo $userMeta->showMessage( __( 'Fields Successfully Saved.', $userMeta->name ) );
        die();
    }
           
}
endif;      
?>