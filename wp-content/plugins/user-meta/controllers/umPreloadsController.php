<?php

if( !class_exists( 'umPreloadsController' ) ) :
class umPreloadsController {
    
    function __construct(){   
        global $userMeta;
        
        add_action( 'init', array( $this, 'loadTextDomain' ) ); 
        
        $userMeta->addScript( 'validationEngine-en.js',         'front', null, 'jquery' );
        $userMeta->addScript( 'validationEngine.js',            'front', null, 'jquery' );  
        $userMeta->addScript( 'validationEngine.css',           'front', null, 'jquery' );
        
        $userMeta->addScript( 'plugin-framework.js',    'front' );
        $userMeta->addScript( 'plugin-framework.css',   'front' );                        
        $userMeta->addScript( 'user-meta.js',           'front' );
        $userMeta->addScript( 'user-meta.css',          'front' );                          
        
        add_filter( 'get_avatar', array( $this, 'getAvatar' ), 10, 5 );
        
        add_filter( 'user_row_actions', array( $this, 'userProfileLink' ), 10, 2 );
        
        add_filter( 'wp_mail_from',                 array( $this, 'mailFromEmail' ) );
        add_filter( 'wp_mail_from_name',            array( $this, 'mailFromName' ) );
        add_filter( 'wp_mail_content_type',         array( $this, 'mailContentType' ) );
                       
        add_action( 'wp_ajax_um_common_request',    array($userMeta, 'ajaxUmCommonRequest' ) );
                   
        add_action( 'admin_notices',                array( $this, 'adminNotices' ) );  
        add_action( 'admin_notices',                array( $userMeta, 'activateLicenseNotice' ) ); 
            
        add_filter( 'pf_file_upload_allowed_extensions', array( $this, 'fileUploadExtensions' ) );
        add_filter( 'pf_file_upload_size_limit',    array( $this, 'fileUploadMaxSize' ) );
        add_filter( 'pf_file_upload_is_overwrite',  array( $this, 'fileUploadOverwrite' ) );
        
        register_activation_hook( $userMeta->file,  array( $this, 'userMetaActivation' ) );
        add_action( 'user_meta_schedule_event',     array( $userMeta, 'clearCache' ) );
    }
  
    function loadTextDomain(){
        global $userMeta;
        load_plugin_textdomain( $userMeta->name, false, basename( $userMeta->pluginPath ) . '/helper/languages' );
    }
    
    /**
     * Filter for get_avatar. Allow to change degault avatar to custom one.
     * 
     * @param type $avatar
     * @param type $id_or_email
     * @param type $size
     * @param type $default
     * @param type $alt
     * @return html img tag
     */
    function getAvatar( $avatar = '', $id_or_email, $size = '96', $default = '', $alt = false ){
        $safe_alt = ( false === $alt) ? '' : esc_attr( $alt );
        
        if ( is_numeric( $id_or_email ) )
	       $user_id = (int) $id_or_email;
        elseif( is_string( $id_or_email ) )
            $user_id = email_exists( $id_or_email );
        elseif( is_object( $id_or_email ) ){
    		if ( !empty( $id_or_email->user_id ) )
    			$user_id = (int) $id_or_email->user_id;
    		elseif ( !empty($id_or_email->comment_author_email) ) 
    			$user_id = email_exists( $id_or_email->comment_author_email );         
        }
            
        if( !isset($user_id) ) return $avatar;
            
        $uploads = wp_upload_dir();
        $umAvatar = get_user_meta( $user_id, 'user_avatar', true );
        if($umAvatar){
            $avatarPath = $uploads['basedir'] . $umAvatar;
            $resizedImage = image_resize( $avatarPath, $size, $size );
            if( !is_wp_error($resizedImage) )
                $avatarPath = $resizedImage;

            $avatarUrl = str_replace( $uploads['basedir'], $uploads['baseurl'], $avatarPath );
            $avatar = "<img alt='{$safe_alt}' src='{$avatarUrl}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
        }
                        
        return $avatar;            
    }
    
    function userProfileLink( $actions, $user_object ){
        global $userMeta;
        $general  = $userMeta->getSettings( 'general' );
        
        if( isset( $general[ 'profile_in_admin' ] ) && !empty( $general[ 'profile_page' ] ) ){
            $url = add_query_arg( 'user_id', $user_object->ID, get_permalink( $general['profile_page'] ) );
            $actions[ 'front_profile' ] = "<a href=\"$url\" target=\"_blank\">" . __( 'Profile', $userMeta->name ) . "</a>";
        }
        
        return $actions;
    }   
    
    function mailFromEmail( $data ){
        global $userMeta;
        $general  = $userMeta->getSettings( 'general' );
        
        if( !empty( $general[ 'mail_from_email' ] ) ){
            if( is_email( $general[ 'mail_from_email' ] ) )
                return $general[ 'mail_from_email' ];
        }
                    
        return $data;
    }
    
    function mailFromName( $data ){
        global $userMeta;
        $general  = $userMeta->getSettings( 'general' );
        
        if( !empty( $general[ 'mail_from_name' ] ) )
            return $general[ 'mail_from_name' ];
                    
        return $data;
    }
    
    function mailContentType( $data ){
        global $userMeta;
        $general  = $userMeta->getSettings( 'general' );
        
        if( !empty( $general[ 'mail_content_type' ] ) )
            return $general[ 'mail_content_type' ];            
                    
        return $data;
    }                    
    
    /**
     * Showing new version availablity notic at user meta admin pages
     */
    function adminNotices(){
        global $current_screen, $userMeta;
        if( $current_screen->parent_base <> 'usermeta' ) return;  
        
        do_action( 'user_meta_admin_notices' );
        
        $currentPlugin = get_site_transient( 'update_plugins' );
        if( isset( $currentPlugin->response[ $userMeta->pluginSlug ] ) ){
            $plugin = $currentPlugin->response[ $userMeta->pluginSlug ];
            echo $userMeta->showMessage( sprintf( __( 'There is a new version of %1$s available. <a href="%2$s">update automatically</a>.', $userMeta->name ), "$userMeta->title $plugin->new_version", $userMeta->pluginUpdateUrl() ) );
        }        
    }
                
    function fileUploadExtensions( $allowedExtensions ){
        global $userMeta;
        
        if( isset( $_REQUEST['field_id'] ) ){
            if( $_REQUEST['field_id'] == 'csv_upload_user_import' ){
                $allowedExtensions = array("csv");
            }elseif( strpos( $_REQUEST['field_id'], 'um_field_' ) !== false ){
               $fieldID = str_replace( "um_field_", "", $_REQUEST['field_id'] );
               $fields = $userMeta->getData( 'fields' );
               if( isset( $fields[$fieldID]['allowed_extension'] ) )
                    $allowedExtensions = explode( ",", $fields[$fieldID]['allowed_extension'] );      
            }
        }     
        
        return $allowedExtensions;   
    }
    
    function fileUploadMaxSize( $sizeLimit ){
        global $userMeta;
        
        if( isset( $_REQUEST['field_id'] ) ){
            if( $_REQUEST['field_id'] == 'csv_upload_user_import' ){
                $sizeLimit = 10 * 1024 * 1024;
            }elseif( strpos( $_REQUEST['field_id'], 'um_field_' ) !== false ){
               $fieldID = str_replace( "um_field_", "", $_REQUEST['field_id'] );
               $fields = $userMeta->getData( 'fields' );
               if( isset( $fields[$fieldID]['max_file_size'] ) )
                    $sizeLimit = $fields[$fieldID]['max_file_size'] * 1024;       
            }
        } 
        return $sizeLimit;       
    }
    
    function fileUploadOverwrite( $replaceOldFile ){
        if( isset( $_REQUEST['field_id'] ) ){
            if( $_REQUEST['field_id'] == 'csv_upload_user_import' )
                $replaceOldFile = true;           
        }  
        return $replaceOldFile;      
    }
    
    function userMetaActivation(){
        wp_schedule_event( current_time( 'timestamp' ), 'daily', 'user_meta_schedule_event');
    }
           
}
endif;      
?>