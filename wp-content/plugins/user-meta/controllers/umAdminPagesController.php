<?php

if( !class_exists( 'umAdminPagesController' ) ) :
class umAdminPagesController {
    
    function __construct(){      
        add_action('admin_menu',    array( $this, 'menuItem' ) );
        add_action('admin_notices', array( $this, 'umAdminNotices' ) );
    }
    
    function menuItem(){
        global $userMeta;
        global $umAdminPages;
                  
        $parentSlug = 'usermeta';
        
        // Top Level Menu
        add_utility_page( 'User Meta', 'User Meta', 'manage_options', $parentSlug, array( $this, 'fields_editor_init' ), $userMeta->assetsUrl . 'images/ump-icon.png' ); 
        
        $pages  = $userMeta->adminPages();
        $isPro  = $userMeta->isPro();
        foreach( $pages as $key => $page ){
            $menuTitle = (!$isPro && !$page['is_free']) ? '<span style="opacity:.5;filter:alpha(opacity=50);">' . $page['menu_title'] . '</span>' : $page['menu_title'];
             $hookName = add_submenu_page( $parentSlug, $page['page_title'], $menuTitle, 'manage_options', $page['menu_slug'], array( $this, $key . '_init' ));
             add_action( 'load-' . $hookName, array( $this, 'onLoadUmAdminPages' ) );
             $pages[$key]['hookname'] = $hookName;
        }
        
        $umAdminPages = $pages;
    }
    
    function onLoadUmAdminPages(){
        do_action( 'user_meta_load_admin_pages' );
    }
    
    function umAdminNotices(){
        global $current_screen;
        
        if( $current_screen->parent_base == 'usermeta' ) 
            do_action( 'user_meta_admin_notices' );
    }
    
    function fields_editor_init(){
        global $userMeta;        
        
         $userMeta->enqueueScripts( array(
            'jquery-ui-core',
            'jquery-ui-widget',
            'jquery-ui-mouse',
            'jquery-ui-sortable',
            'jquery-ui-draggable',
            'jquery-ui-droppable',
             
            'plugin-framework', 
            'user-meta',
            'validationEngine',
        ) );                      
        $userMeta->runLocalization();     
  
        $fields = $userMeta->getData( 'fields' );   
        $userMeta->render( 'fieldsEditorPage', array(
            'fields'    => $fields
        ) );       
    }
    
    function forms_editor_init(){
        global $userMeta;     
        
         $userMeta->enqueueScripts( array(
            'jquery-ui-core',
            'jquery-ui-widget',
            'jquery-ui-mouse',
            'jquery-ui-sortable',
            'jquery-ui-draggable',
            'jquery-ui-droppable',
             
            'plugin-framework', 
            'user-meta',
            'validationEngine',
        ) );                      
        $userMeta->runLocalization();
        
        $forms  = $userMeta->getData( 'forms' );           
        $fields = $userMeta->getData( 'fields' );   
        $userMeta->render( 'formsEditorPage', array(
            'forms'     => $forms,
            'fields'    => $fields
        ) );        
    }
    
    function email_notification_init(){
        global $userMeta;
        
        $userMeta->enqueueScripts( array(
            'jquery-ui-core',
            'jquery-ui-tabs',
            'jquery-ui-all',

            'plugin-framework', 
            'user-meta',
        ) );                      
        $userMeta->runLocalization();
                
        $data = array(
            'registration'          => $userMeta->getEmailsData( 'registration' ), 
            'profile_update'        => $userMeta->getEmailsData( 'profile_update' ),
            'activation'            => $userMeta->getEmailsData( 'activation' ),
            'deactivation'          => $userMeta->getEmailsData( 'deactivation' ),
            'email_verification'    => $userMeta->getEmailsData( 'email_verification' ),
            'lostpassword'          => $userMeta->getEmailsData( 'lostpassword' ),
        );
        
        $userMeta->renderPro( 'emailNotificationPage', array(
            'data'      => $data,
            'roles'     => $userMeta->getRoleList(),
        ), 'email' );         
    }
    
    function export_import_init(){
        global $userMeta;     
        
        $userMeta->enqueueScripts( array( 
            'jquery-ui-core',
            'jquery-ui-sortable',
            'jquery-ui-draggable',
            'jquery-ui-droppable',
            'jquery-ui-datepicker',
            'jquery-ui-dialog',
            'jquery-ui-progressbar',
            
            'plugin-framework', 
            'user-meta',           
            'jquery-ui-all',
            'fileuploader',            
        ) );                      
        $userMeta->runLocalization();
        
        $cache = $userMeta->getData( 'cache' );            
        $csvCache = @$cache['csv_files'];
                           
        //importPage            
        $userMeta->renderPro( 'importExportPage', array(
            'csvCache'  => $csvCache,
            'maxSize'   => (20 * 1024 * 1024), //20M
        ), 'exportImport' );          
    }
    
    function settings_init(){
        global $userMeta;
        
        $userMeta->enqueueScripts( array(
            'jquery-ui-core',
            'jquery-ui-widget',
            'jquery-ui-mouse',
            'jquery-ui-sortable',
            'jquery-ui-draggable',
            'jquery-ui-droppable',
            'jquery-ui-accordion',
            'jquery-ui-tabs',
            'jquery-ui-all',

            'plugin-framework', 
            'user-meta',
            'validationEngine',
        ) );                      
        $userMeta->runLocalization();
        
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
    

}
endif;      
?>