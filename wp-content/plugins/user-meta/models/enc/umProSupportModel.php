<?php

if( !class_exists( 'umProSupportModel' ) ) :
class umProSupportModel {
           
    /**
     * Loading all controllers. load pro controllers if pro
     */
    function loadControllers(){
        global $userMeta;
        $controllersOrder = $userMeta->controllersOrder();
        
        $userMeta->isPro = false;  // manually
                                         
        $classes = array();
        foreach( scandir( $userMeta->controllersPath ) as $file ) {
            if( preg_match( "/.php$/i" , $file ) )
                $classes[ str_replace( ".php", "", $file ) ] = $userMeta->controllersPath . $file;            
        }          
        
        if( @$userMeta->isPro && $userMeta->isPro() ){
            $proDir = $userMeta->controllersPath . 'pro/';
            if( file_exists( $proDir ) ){
                foreach( scandir( $proDir ) as $file ) {
                    if( preg_match( "/.php$/i" , $file ) )
                        $classes[ str_replace( ".php", "", $file ) ] = $proDir . $file; 
                }                  
            }          
        }       
               
        foreach( $classes as $className => $classPath ){
            require_once( $classPath );
            if( !in_array( $className, $controllersOrder ) )
                $controllersOrder[] = $className;
        }
                          
        foreach( $controllersOrder as $className ){
            if( class_exists( $className ) )
                $instance[] = new $className;
        }
              
        return $instance;        
    }     

    /**
     * Calling views. if pro then render from pro directory
     */
    function renderPro( $viewName, $parameter = array(), $subdir=null ){
        global $userMeta;        
        
        $subdir = $subdir ? "$subdir/" : null;
        if( @$userMeta->isPro AND self::isPro() AND file_exists( $userMeta->viewsPath . "pro/$subdir" . $viewName . '.php' ) )
            $viewPath = $userMeta->viewsPath . "pro/$subdir" . $viewName . '.php';
        else{
            $viewPath = $userMeta->viewsPath . $subdir . $viewName . '.php';
            if( !file_exists( $viewPath ) ) return;
        }
        
        if( $parameter ) extract($parameter);            
        $pageReturn = include $viewPath;
        if( $pageReturn AND $pageReturn <> 1 )
            return $pageReturn;
        if( @$html ) return $html;        
    }   
    
    /**
     * Check if pro version is validated
     */
    function isPro(){
        global $userMeta;        
        if( $userMeta->isPro && self::isLicenceValidated() )
            return true;
        return false;    
    }    
    
    /**
     * Check is license validated
     */
    function isLicenceValidated(){
        $auth = self::getProAuth();
        return !empty( $auth['valid'] ) ? true : false;         
    }
    
    /**
     * Generating proKey
     */
    function generateProKey( $key, $text ){
        if( !( function_exists( 'mcrypt_encrypt' ) ) ) return false;
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(md5(md5($key))), $text, MCRYPT_MODE_CBC, md5(md5($key))));        
    }
    
    /**
     * Generate Validation Key to store at status
     */
    private function generateValidationKey( $email ){
        $status = self::generateProKey( $email, 'validated' );
        if( empty( $status ) )
            $status = md5( 'validated' );
        
        return $status; 
    }
    
    /**
     * Sleep for 1.1.3rc3
     */
    function validateProKey( $email, $pass ){
        global $userMeta;
        
        $msg = array(
            'validated'     => __( 'Successfully validated', $userMeta->name ),
            'noaccess'      => sprintf( __( 'Please purchase %s', $userMeta->name ), "<a href=\"{$userMeta->website}\">User Meta Pro</a>" ),
            'invalid_pass'  => __( 'Invalid email or password.', $userMeta->name ),
            'not_found'     => __( 'Email not found.', $userMeta->name ),
        );
        
        include_once( ABSPATH . 'wp-includes/class-IXR.php' );
        
        $client = new IXR_Client( $userMeta->website . '/xmlrpc.php' );    
        
        if ( !$client->query( 'ump.checkUserAuth', $email, base64_decode( $pass ), network_site_url(), $userMeta->version ) )
            $response = __( 'Something went wrong', $userMeta->name ) . ' - ' . $client->getErrorCode().' : '.$client->getErrorMessage();
        else
            $response = $client->getResponse();     
        
        if( $response == 'validated' )
            $return[ 'validate' ] = true;
        $return[ 'msg' ] = isset( $msg[ $response ] ) ? $msg[ $response ] : $response;
        
        return $return;
    }
    
    /**
     * Sending xmlrpc request to UMP site to validate license.
     * 
     * @param $email: Email for UMP site
     * @param $pass: Pass for UMP site
     * @param $requestType: activate | cred_update | version_update | license_withdrawn
     * @return WP_Error object | (string) response text
     */
    private function xmlRpcValidation( $email, $pass, $requestType ){
        global $userMeta;
        
        include_once( ABSPATH . 'wp-includes/class-IXR.php' );
        
        $client = new IXR_Client( $userMeta->website . '/xmlrpc.php' );    
        
        if ( !$client->query( 'ump.checkUserAuth', 
                $email, 
                base64_decode( $pass ), 
                network_site_url(), 
                $userMeta->version,
                $requestType ) ){
            $response = 'Something went wrong' . ' - ' . $client->getErrorCode().' : '.$client->getErrorMessage();       
            $response = new WP_Error( 'error', $response );
        }else
            $response = $userMeta->convertErrorArray( $client->getResponse() );    
        
        return $response;
    }
    
    /**
     * Remotely validate pro version from UMP site. 
     * 
     * @global type $userMeta
     * @param type $args
     * @return string 
     */
    function remoteValidationPro( $args ){
        global $userMeta;
        
        $username   = @$args[0];
        $pass       = @$args[1]; 
        $auth       = @$args[2];         
         
        $user = get_user_by( 'login', $username );
        if( $user === false )
            new WP_Error("not_user", "Username: $username is not exists");
        
        if( ! $userMeta->isAdmin( $user->ID ) )
            return new WP_Error("not_admin", "Username: $username is not an admin account");
        
        $user = wp_authenticate( $username, $pass );
        if( is_wp_error( $user ) )
            return $user;
        
        if( empty( $auth['status'] ) )
            return new WP_Error("not_validated", "Something went wrong - Not Validated");

        if( $auth['status'] == 'validated' ){
            self::updateProAuth( $auth['email'], $auth['key'] );
            return self::isPro() ? 'Pro version already validated. Credentials updated!' : 'License successfully validated';
        }elseif( $auth['status'] == 'license_withdrawn' ){
             if( !self::isPro() ){
                return "No license found to withdraw";
            }else {
                if( $userMeta->updateData( 'pro_auth', null ) );
                    return "License has been withdrawn";               
            }           
        } 
        
        return new WP_Error("not_validated", "Something went wrong - Not Validated");
    }
    
    /**
     * Generating proUrl for validate or download pro version
     */
    function generateProUrl( $action, $version=null, $text=null, $key=null ){
        global $userMeta;
        
        if( !$text || !$key ){        
            $auth  = self::getProAuth();
            $text   = @$auth['key'];
            $key    = @$auth['email'];
            if( !@$auth['valid'] ) return false;
        }        
                       
        $remoteUrl   = $userMeta->website . "/remote-download/";
        $accessKey  = self::generateProKey( $key, $text );
        $key2       = self::generateProKey( $key, time() );
        $siteUrl    = network_site_url();
        $remoteUrl .= "?action=$action&email=$key&key=$accessKey&key2=$key2&site=$siteUrl";
        if( $version )
            $remoteUrl .= "&prev_version=$userMeta->version&version=$version";
        return $remoteUrl;
    }     
       
    /**
     * Update pro account settings
     */
    function updateProAccountSettings( $data ){
        global $userMeta;            
      
        if( empty($data['account_email']) || empty($data['account_pass']) )
            return false;
        
        $email          = $data['account_email'];   
        $key            = base64_encode( $data['account_pass'] );
        $requestType    = self::isPro() ? 'cred_update':'activate';
        $validation = self::xmlRpcValidation( $email, $key, $requestType );  
        
        if( is_wp_error( $validation ) )
            return $userMeta->printAjaxOutput( $userMeta->showError( $validation ) );  
                
        if( $validation == 'validated' ){
            self::updateProAuth( $email, $key );
            
            if( $requestType == 'activate' ){
                echo $userMeta->showMessage( 'License successfully validated' );
                if( ! $userMeta->isPro ){
                    $msg  = 'Redirecting for upgrading to Pro version... ';
                    $msg .= "<br />".  'if not redirecting,' ." <a href='" . $userMeta->pluginUpdateUrl() . "'>" . 'click here for upgrading to Pro version' . "</a>";
                    echo $userMeta->showMessage( $msg );
                    echo $userMeta->jsRedirect( $userMeta->pluginUpdateUrl() );
                }else{
                    echo $userMeta->jsRedirect( $userMeta->adminPageUrl( 'settings', false ) );
                }                 
            }else
                echo $userMeta->showMessage( 'Credentials updated' );
        }else
            echo $userMeta->showError( 'Something went wrong!' );
            
        die();                               
    }   
    
    /**
     * get pro authorization data
     */
    private function getProAuth(){
        global $userMeta;
        
        $auth = $userMeta->getData( 'pro_auth' );     
        if( !empty( $auth['email'] ) && !empty( $auth['status'] ) ){
            if( self::generateValidationKey($auth['email']) == $auth['status'] )
                 $auth[ 'valid' ] = true;
        }
     
        return $auth;
    }  
    
    /**
     * Update ProAuth 
     * @param  (array) $data, keys: email, key, status: validated
     * 
     */
    private function updateProAuth( $email, $key ){
        global $userMeta;
        
        $auth = array(
            'email'         => $email,
            'key'           => $key,               
            'status'        => self::generateValidationKey( $email ),
            'version'       => $userMeta->version,
            'last_checked'  => time(),
        );                
        return $userMeta->updateData( 'pro_auth', $auth );           
    }
    
    /**
     * Sleep from 1.1.3rc3
     */
    function remoteValidatePro( $args ){
        global $userMeta;
        
        $username   = @$args[0];
        $pass       = @$args[1]; 
        $auth       = @$args[2];
        
        if( ! user_pass_ok( $username, $pass ) )
             return "Username and password doesn't match for your site"; 
         
        $user = get_user_by( 'login', $username );
        if( ! $userMeta->isAdmin( $user->ID ) )
            return "Username: $username is not an admin account";

        if( @$auth['status'] == 'validated' ){
            if( self::isPro() ){
                return "Already Validated";
            }else {
                $userMeta->updateData( 'pro_auth', $auth );
                return "Successfully validated";               
            }
        }   
        return "Not Validated";
    }
    
    function withdrawLicense(){
        global $userMeta;
        
        if( !$userMeta->isAdmin() ) 
            return new WP_Error( 'not_admin', 'An admin account is needed to withdraw the pro license' );
    
        $auth = $userMeta->getData( 'pro_auth' ); 
        if( empty( $auth['email'] ) || empty( $auth['key'] ) )
            return new WP_Error( 'email_blank', 'Please update your email and password before withdraw the pro license' );

        $response = self::xmlRpcValidation( $auth['email'], $auth['key'], 'license_withdrawn' );
        if( is_wp_error( $response ) )
            return $response;
        
        if( $response == 'validated' )
            return $userMeta->updateData( 'pro_auth', null );
        
        return false;
    }
    
    /**
     * Upgrading from ''1.1.2rc3', '1.1.2rc4', '1.1.2', '1.1.3rc1', '1.1.3rc2'
     */
    function changeAuthProStructure(){
        global $userMeta;
        
        $auth = $userMeta->getData( 'pro_auth' );
        if( empty($auth['email']) || empty($auth['key']) || empty($auth['status']) )
            return false;

        if( $auth['status'] == 'validated' )
            return self::updateProAuth( $auth['email'], $auth['key'] );
        
        return false;
    }
    
    function notifyVersionUpdate(){
        global $userMeta;
        
        $auth = $userMeta->getData( 'pro_auth' );
        if( !empty( $auth['email'] ) && !empty( $auth['key'] ) )
            self::xmlRpcValidation( $auth['email'], $auth['key'], 'version_update' );
    }
    
    /**
     * Showing admin notice to all admin page when pro version is not validated
     */
    function activateLicenseNotice(){
        global $userMeta;            
        if( $userMeta->isPro ){
            if( !$userMeta->isPro() )
                echo $userMeta->showError( sprintf( __( 'Please enter your license information to <a href="%s">activate User Meta Pro</a>.', $userMeta->name ), admin_url( "admin.php?page=user-meta-settings#um_activation_form" ) ) );
        }        
    }
}
endif;