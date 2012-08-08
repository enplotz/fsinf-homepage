<?php
if (!class_exists( 'PluginFrameworkWPSupport' )) :
class PluginFrameworkWPSupport {
    
    /**
     * Generate nonce field
     */
    function nonceField(){
        global $pluginFramework;
        return wp_nonce_field( $pluginFramework->settingsArray('nonce'), 'pf_nonce', true, false );
    }        
            
    function verifyNonce( $adminOnly=true ){
        global $pluginFramework;
        $nonce      = @$_REQUEST['pf_nonce'];
        $nonceText  = $pluginFramework->settingsArray('nonce');
        if( !wp_verify_nonce( $nonce, $nonceText ) ) die( __( 'Security check', $pluginFramework->name ) );     
        
        if( $adminOnly ){
            if( !( $this->isAdmin() ) )
                die( __( 'Security check', $pluginFramework->name ) );
        }  
                               
        return true;      
    }
    
    function actionName( $actionName ){
        return "<input type=\"hidden\" name=\"method_name\" value=\"$actionName\">";
    }
    
    function isAdmin($userID=null){          
        if( $userID )
            return user_can( $userID, 'administrator' );
            
        global $user_ID; 
        return user_can( $user_ID, 'administrator' );               
    }
    
    function isAdminSection(){
        if( !is_admin() )
            return false;
            
        $ajaxurl = admin_url( 'admin-ajax.php' );
        $pos     = strpos( $ajaxurl, $_SERVER[ 'REQUEST_URI' ] );
        return ( $pos === false ) ? true : false;
    }
    
    function getRoleList(){
        global $wp_roles;
        return $wp_roles->role_names;
    }
    
    function getUserRole( $userID ) {
    	$user = new WP_User( $userID );
        if( is_wp_error( $user ) ) return false;
                
    	$user_roles    = @$user->roles;
        if( is_array( $user_roles ) )
            return array_shift($user_roles);
            
    	return false;
    }        
  

    /**
     * Check value is unique for given field.
     *  
     * If more then one user found with given field and value, this method will return true for first user false for all other users.
     * 
     * @param type $fieldName
     * @param type $fieldValue 
     * @param type $comparingID : if no $comparingID is given, $user_ID or $_REQUEST['user_id'] will use for compare.
     * @return boolean 
     */
    function isUserFieldAvailable( $fieldName, $fieldValue, $comparingID=null ){
        global $user_ID, $pluginFramework, $wpdb;           
        $unique = true;            
        $wpUserTable = $pluginFramework->wpUserTableFieldsArray();  
   
        // Set $comparingID if not set
        if( !$comparingID ){
            $comparingID = $user_ID;
            $comparingID = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : $comparingID;
        }
          

        if( $fieldName == 'user_login' ) :
            $fieldValue = sanitize_user( $fieldValue, true );
            if( !function_exists( 'username_exists' ) )
                require_once(ABSPATH . WPINC . '/registration.php');
            $user_id = username_exists( $fieldValue );                
        elseif( $fieldName == 'user_email' ) :
            $fieldValue = sanitize_email( $fieldValue );
            if( !function_exists( 'email_exists' ) )
                require_once(ABSPATH . WPINC . '/registration.php');
            $user_id = email_exists( $fieldValue );                 
        // Check from wp_users table
        elseif( in_array( $fieldName, $wpUserTable ) ) :
            $user_id = $wpdb->get_var( $wpdb->prepare( 
            	"SELECT ID FROM $wpdb->users WHERE $fieldName = %s", $fieldValue        	
            ) );                 
        // Check by usermeta     
        else :
            $users = get_users( "meta_key=$fieldName&meta_value=$fieldValue&meta_compare='='" ) ;  
            if( count($users) > 0 )
                $user_id = $users[0]->ID;                           
        endif;
                    
        if( isset($user_id) ){
            if( $user_id ){
                if( $user_id <> $comparingID )
                    $unique = false;
            }
        }            
        return $unique;
    }
    
    
    /**
     * Add or update user
     * @param array $data: data need to update, both userdata and metadata
     * @param int $userID: if not set, user will registered else user update
     */
    function insertUser( $data, $userID=null ){
        global $pluginFramework, $wpdb;
        $errors = new WP_Error();
        
        // Determine Fields
        $userdata = array();
        $metadata = array();            
        $wpField = $pluginFramework->defaultUserFieldsArray();
        foreach( $data as $key => $val ){
            $key = is_string($key) ? trim($key) : $key;
            $val = is_string($val) ? trim($val) : $val;
            if( !$key ) continue;
            if( isset($wpField[$key]) )
                $userdata[$key] = $val;
            else
                $metadata[$key] = $val;
        }
        
        // sanitize email and user
        if( @$userdata['user_email'] ) 
            $userdata['user_email'] = sanitize_email( $userdata['user_email'] );                
        if( @$userdata['user_login'] ) 
            $userdata['user_login'] = sanitize_user( $userdata['user_login'], true );    
         
        // Case of registration
        if( !$userID ){     
            if( @$userdata['user_email'] && !@$userdata['user_login'] ){
                $user_login = explode( '@', $userdata['user_email'] );
                $user_login = $user_login[0];
                if( username_exists( $user_login ) )
                    $user_login = $user_login . rand(1,999);                
                $userdata['user_login'] = sanitize_user( $user_login, true);
            }elseif( @$userdata['user_login'] && !@$userdata['user_email'] ){
                $userdata['user_email'] = $userdata['user_login'] .'@noreply.com'; 
            }elseif( !@$userdata['user_login'] && !@$userdata['user_email'] ){ 
                $errors->add( 'empty_login_email', __( 'Cannot create a user with an empty login name and empty email', $pluginFramework->name ) );  
            }
            
            if( !@$userdata[ 'user_pass' ] ){
                $userdata[ 'user_pass' ] = wp_generate_password( 12, false);
                $passwordNag = true;
            } 
                     
            $userdata[ 'user_email' ] = apply_filters( 'user_registration_email', @$userdata[ 'user_email' ] );
        	do_action( 'register_post', @$userdata[ 'user_login' ], @$userdata[ 'user_email' ], $errors );        
        	$errors = apply_filters( 'registration_errors', $errors, @$userdata[ 'user_login' ], @$userdata[ 'user_email' ] );
            
        	if ( $errors->get_error_code() )
        		return $errors;            
                           
            $user_id = wp_insert_user( $userdata );
        	if ( is_wp_error( $user_id ) ) 
                return $user_id;
             
            if( @$passwordNag )
                update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.     
                                     
        }else{
            $userdata['ID'] = $userID;
            $user_id = wp_update_user( $userdata );
        	if ( is_wp_error( $user_id ) ) 
                return $user_id;            
        }      
                        
        // Update metadata   
        if( $metadata ){
			foreach ($metadata as $key => $value) {
                if( $userID )
                    update_user_meta( $user_id, $key, $value );
                else
                    add_user_meta( $user_id, $key, $value );
            }                            
        }  

        $userdata[ 'ID' ] = $user_id;                    
        return array_merge( $userdata, $metadata );                            
    }
    
          
    /**
     * Non-ajax file upload
     * 
     * @param string $fieldName
     * @param array | string $extensions array('jpg','png','gif') | jpg,png,gif
     * @param int $maxSize 1048576 (in Byte 2MB default)
     * @param boolean $replaceOldFile False
     * @return WP_Error|string 
     */
    function fileUpload( $fieldName, $extensions=array('jpg','png','gif'), $maxSize=1048576, $replaceOldFile=false ){    
        global $pluginFramework;
               
        $uploads        = wp_upload_dir();
        $uploadPath     = $uploads['path'] . '/';
        $uploadUrl      = $uploads['url']  . '/';       
        
        if( !isset( $_FILES[ $fieldName ][ 'name' ] ) )
            return new WP_Error( 'no_field', __( 'No file upload field found!', $pluginFramework->name ) );
        
        $file = $_FILES[ $fieldName ];
              
        $size = $file[ 'size' ];
        
        if($size == 0)
            return new WP_Error( 'no_file', __( 'File is empty.', $pluginFramework->name ) );
        
        if( !is_writable( $uploadPath ) )
            return new WP_Error( 'not_writable', __( 'Server error. Upload directory is not writable.', $pluginFramework->name ) );        
             
        if( $size > $maxSize )
            return new WP_Error( 'max_size', sprintf( __( 'File %s is too large.', $pluginFramework->name ), $file['name'] ) );
                   
        $pathInfo   = pathinfo( $file[ 'name' ] );
        $fileName   = $pathInfo[ 'filename' ];
        $fileName   = str_replace( " ", "-", $fileName );
        $ext        = $pathInfo[ 'extension' ];        
        
        if( is_string( $extensions ) )
            $extensions = explode( ",", $extensions );
        
        if( is_array( $extensions ) ){
            $extensions = array_map( "trim", $extensions );
            $extensions = array_map( "strtolower", $extensions );       
        }
        
        if( $extensions && !in_array( strtolower( $ext ), $extensions ) ){
            $these = implode(', ', $extensions );
            return new WP_Error( 'invalid_extension', sprintf( __( 'File %1$s has an invalid extension, it should be one of %2$s.', $pluginFramework->name ),$file['name'], $these ) );
        }        
      
        /// don't overwrite previous files that were uploaded
        if( !$replaceOldFile ){            
            while ( file_exists( $uploadPath . $fileName . '.' . $ext ) )
                $fileName .= time();
        }
        
        $fileName = $fileName . '.' . $ext;
         
        if( !move_uploaded_file( $file[ 'tmp_name' ], $uploadPath . $fileName ) ){
            return new WP_Error( 'error', __( 'Could not save uploaded file.', $pluginFramework->name ) .
                __( 'The upload was cancelled, or server error encountered', $pluginFramework->name ) );
        }
        
        $filepath = $uploads['subdir'] . "/" . $fileName;
        
        return $filepath;             
    }        
    

    //sleep
    function donation(){
        //return 'Donate Now';
        return "
        <p>If you find this plugins useful, please consider making a donation to keep the coffee brewing.</p>    
        <p><a href='http://khaledsaikat.com/donate-now/'>Donate</a></p>
        <p>Thanks for your support, <a href='http://khaledsaikat.com'>Khaled Saikat</a></p>
        ";
                
    }         
    
    //sleep
    function runTest(){
        global $pluginFramework;
        echo "Framework Version: " .    $pluginFramework->frameworVersion   ."<br />";
        echo "Framework Url: " .        $pluginFramework->frameworkUrl           ."<br />";
        echo "Framework Path: " .       $pluginFramework->frameworkPath           ."<br />";
        echo "Framework Model Path: " . $pluginFramework->modelsPath ."<br />";
        echo "Framework Assets URL: " . $pluginFramework->assetsUrl ."<br />";
    }
         
    
    
    /**
     * Create meta box 
     * Should be called inside of 'metabox-holder' class div
     */
    function metaBox( $title, $content, $deleteIcon=false, $isOpen=true ){
        global $pluginFramework;
        return $pluginFramework->render( 'metaBox', array( 
            'title'     => $title, 
            'content'   => $content, 
            'deleteIcon'=> $deleteIcon,
            'isOpen'    => $isOpen,
        ) );
        return $html;
    } 
    
    /**
     * Print the output to browser if it is ajax request. and run die() immediately.
     */
    function printAjaxOutput( $html ){
        if( !empty( $_REQUEST[ 'is_ajax' ] ) ){
            echo $html;
            die();
        }else
            return $html;        
    }
    
    /**
     * Showing error. 
     * @param   : string | WP_Error
     * @return  : echo error if is_ajax | return error
     */
    function showError( $errors ){
        $html   = null;
        
        if( is_string( $errors ) )
            $html = $errors;
        elseif( is_wp_error( $errors ) ){
            foreach( $errors->get_error_messages() as $error )
                $html .= "<div>$error</div>";
        }
                
        $html = $this->isAdminSection() ? "<div class='error'><p>$html</p></div>" : "<div class='pf_error'>$html</div>";
        return $this->printAjaxOutput( $html );
    }
    
    function showMessage( $message, $type='success' ){
        $class = 'pf_' . $type;
        if( $this->isAdminSection() && ( $type == 'success' ) )
            return "<div class='updated'><p>$message</p></div>";
        return "<div class='$class'>$message</div>";
    }        
    
    function userLogin( $user_login=null, $user_pass=null, $remember=false ){
        $creds = array();
        $creds['user_login']    = $user_login ? $user_login : @$_REQUEST['user_login'];
        $creds['user_password'] = $user_pass ? $user_pass : @$_REQUEST['user_pass'];
        $creds['remember']      = isset($remember) ? $remember : @$_REQUEST['remember'];
        
        if( !$creds['user_password'] ) return;           
        return wp_signon( $creds, false );           
    }        
    
    /**
     * Apply filter 'login_redirect' to get login redirection url
     * Code get form wp-login.php, line 551-570
     * @param $user: WP_User object
     * @return string $redirect_to
     */
    function applyFiltersLoginRedirection( $user ){
        if ( isset( $_REQUEST['redirect_to'] ) ) {
        	$redirect_to = $_REQUEST['redirect_to'];
        	// Redirect to https if user wants ssl
        	if ( @$secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
        		$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
        } else {
        	$redirect_to = admin_url();
        }        
        
        return apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);        
    }  
    
    function applyFiltersLogoutRedirection( $redirect_to = null ){
        $redirect_to = $redirect_to ? $redirect_to : @$_REQUEST['redirect_to'];
        $redirect_to = $redirect_to ? $redirect_to : 'wp-login.php?loggedout=true';
        return apply_filters( 'logout_redirect', $redirect_to );
    }   
    
    function applyFiltersRegistrationRedirection( $user_id ){
        return apply_filters( 'registration_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user_id );
    }          
    
    function jsRedirect( $redirect_to ){
        if( ! $redirect_to ) return;

        //if ( 0 === strpos( $redirect_to, 'http') )
            //$redirect_to = site_url( $redirect_to );
        //return $redirect_to;
                
        $html = null;
        $html .= "<script type=\"text/javascript\">";
        $html .= "window.location.href = \"$redirect_to\";";
        $html .= "</script>";
        return $html;
    }   
    
    function jQueryRolesTab( $tabID, $tabs=array(), $tabsData=array(), $default=null ){       
        $html = "<ul>";
        foreach( $tabs as $key => $val )
            $html .= "<li><a href=\"#$tabID-tab-$key\">$val</a></li>";
        $html .= "</ul>";  
        foreach( $tabs as $key => $val ){
            $data = isset( $tabsData[ $key ] ) ? $tabsData[ $key ] : $default;
            $html .= "<div id=\"$tabID-tab-$key\">$data</div>";   
        }
        
        $html = "<div id=\"$tabID-tabs\">$html</div>";        
        
        $js = "
            <script type=\"text/javascript\">
                jQuery(document).ready(function(){
                    jQuery( \"#$tabID-tabs\" ).tabs();
                });
            </script>        
        ";   
        
        return  $html . $js;
    }
    
    /**
     * Send email
     * @param : array $data, keys: email, subject, body, from_email, from_name, format
     */
    function sendEmail( $data ){
        if( empty( $data[ 'email' ] ) )     return;
        if( empty( $data[ 'subject' ] ) )   return;
        
        if( ! empty( $data[ 'from_email' ] ) ){
            global $fromEmail;
            $fromEmail = $data[ 'from_email' ];
            add_filter( 'wp_mail_from', create_function( '', 'global $fromEmail;return $fromEmail;' ), 40 );
        }
        
        if( ! empty( $data[ 'from_name' ] ) ){
            global $fromName;
            $fromName = $data[ 'from_name' ];
            add_filter( 'wp_mail_from_name', create_function( '', 'global $fromName;return $fromName;' ), 40 );      
        }  
        
        if( ! empty( $data[ 'format' ] ) ){
            global $mailFormat;
            $mailFormat = $data[ 'format' ];
            add_filter( 'wp_mail_content_type', create_function( '', 'global $mailFormat;return $mailFormat;' ), 40 );      
        }          
        
        //if( @$data[ 'format' ] == 'text/html'  )
            //add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ), 40 );
                      
        wp_mail( $data[ 'email' ], $data[ 'subject' ], @$data[ 'body' ] );
    }
                
}
endif;
?>