<?php

if( !class_exists( 'umSupportArray' ) ) :
class umSupportArray {
    
    function controllersOrder(){
        return array(
            'umPreloadsController',
            'umPreloadsProController',
            'umBackendProfileController',
            'umShortcodesController',
            'umFieldsController',
            'umFormsController',
            'umEmailNotificationController',
            'umUserImportController',
            'umSettingsController'          
        );
    }
    
    function umFields(){   
        global $userMeta;
        
        $fieldsList = array(
        
            //WP default fields
            'user_login' => array(
                'title'         => __( 'Username', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true, 
            ),
            'user_email' => array(
                'title'         => __( 'Email', $userMeta->name ),
                'field_group'   => 'wp_default',
                'is_free'       => true,
            ),   
            'user_pass' => array(
                'title'         => __( 'Password', $userMeta->name ),
                'field_group'   => 'wp_default',
                'is_free'       => true,
            ),   
            /*'user_nicename' => array(
                'title'         => 'Nicename',
                'field_group'     => 'wp_default', 
            ), */            
            'user_url' => array(
                'title'         => __( 'Website', $userMeta->name ),
                'field_group'   => 'wp_default',
                'is_free'       => true,
            ),   
            'display_name' => array(
                'title'         => __( 'Display Name', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true, 
            ),   
            'nickname' => array(
                'title'         => __( 'Nickname', $userMeta->name ),
                'field_group'   => 'wp_default',   
                'is_free'       => true,
            ),   
            'first_name' => array(
                'title'         => __( 'First Name', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true,
            ),   
            'last_name' => array(
                'title'         => __( 'Last Name', $userMeta->name ),
                'field_group'   => 'wp_default',   
                'is_free'       => true,
            ),   
            'description' => array(
                'title'         => __( 'Biographical Info', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true, 
            ),   
            'user_registered' => array(
                'title'         => __( 'Registration Date', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true,
            ),   
            'role' => array(
                'title'         => __( 'Role', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true,
            ),   
            'jabber' => array(
                'title'         => __( 'Jabber', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true,
            ),   
            'aim' => array(
                'title'         => __( 'Aim', $userMeta->name ),
                'field_group'   => 'wp_default', 
                'is_free'       => true, 
            ),   
            'yim' => array(
                'title'         => __( 'Yim', $userMeta->name ),
                'field_group'   => 'wp_default',
                'is_free'       => true,
            ),   
            'user_avatar' => array(
                'title'         => __( 'Avatar', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true,
            ),             
            
         
            //Standard Fields
            'text' => array(
                'title'         => __( 'Textbox', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,   
            ),   
            'textarea' => array(
                'title'         => __( 'Paragraph', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,   
            ),   
            'rich_text' => array(
                'title'         => __( 'Rich Text', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,   
            ),  
            'hidden' => array(
                'title'         => __( 'Hidden Field', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,   
            ),                       
            'select' => array(
                'title'         => __( 'Drop Down', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,   
            ),   
            'checkbox' => array(
                'title'         => __( 'Checkbox', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,  
            ),   
            'radio' => array(
                'title'         => __( 'Select One (radio)', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,  
            ),     
            'datetime' => array(
                'title'         => __( 'Date / Time', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => false,
            ),                      
            'password' => array(
                'title'         => __( 'Password', $userMeta->name ),
                'field_group'   => 'standard', 
                'is_free'       => false,
            ),    
            'email' => array(
                'title'         => __( 'Email', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => false,
            ),             
            'file' => array(
                'title'         => __( 'File Upload', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => false,
            ), 
            'image_url' => array(
                'title'         => __( 'Image URL', $userMeta->name ),
                'field_group'   => 'standard',  
                'is_free'       => false,
            ),                   
            'phone' => array(
                'title'         => __( 'Phone Number', $userMeta->name ),
                'field_group'   => 'standard',  
                'is_free'       => false,
            ), 
            'number' => array(
                'title'         => __( 'Number', $userMeta->name ),
                'field_group'   => 'standard', 
                'is_free'       => false, 
            ), 
            'url' => array(
                'title'         => __( 'Website', $userMeta->name ),
                'field_group'   => 'standard', 
                'is_free'       => false,
            ),                          
            'country' => array(
                'title'         => __( 'Country', $userMeta->name ),
                'field_group'   => 'standard', 
                'is_free'       => false,
            ),      
            /*'scale' => array(
                'title'         => 'Scale',
                'field_group'     => 'standard',  
            ),*/           
            
            
            //Formating Fields
            'page_heading' => array(
                'title'         => __( 'Page Heading', $userMeta->name ),
                'field_group'   => 'formatting',  
                'is_free'       => false,
            ),                                     
            'section_heading' => array(
                'title'         => __( 'Section Heading', $userMeta->name ),
                'field_group'   => 'formatting',  
                'is_free'       => false,
            ),                                                                        
            'html' => array(
                'title'         => __( 'HTML', $userMeta->name ),
                'field_group'   => 'formatting',  
                'is_free'       => false,
            ),                                     
            'captcha' => array(
                'title'         => __( 'Captcha', $userMeta->name ),
                'field_group'   => 'formatting',  
                'is_free'       => false,
            ),                                                         
        );        
        return $fieldsList;                    
    }    
    

    function isValidFormType( $type ){
        $data = array(
            'profile', 'registration', 'both', 'none', 'login'
        );
        return in_array( $type , $data ) ? true : false;
    }
    
    function loginByArray(){
        global $userMeta;
        return array( 
            'user_login' => __( 'Username', $userMeta->name ),
            'user_email' => __( 'Email', $userMeta->name ),
            'user_login_or_email' => __( 'Username or Email', $userMeta->name ),
        );
    }
    
    function defaultSettingsArray( $key=null ){
        $settings = array(
        
            'general' => array(),
        
            'login' => array(
                'login_by'          => 'user_login',
                'login_form'        => "%login_form%\n%lostpassword_form%",
                'loggedin_profile' => array(
                    'administrator' => "<p>Hello %user_login%</p>\n<p>%avatar%</p>\n<p><a href=\"%admin_url%\">Admin Section</a></p>\n<p><a href=\"%logout_url%\">Logout</a></p>",
                    'subscriber'    => "<p>Hello %user_login%</p>\n<p>%avatar%</p>\n<p><a href=\"%logout_url%\">Logout</a></p>",
                ),
            ),  
            
            'registration' => array(
                'user_activation'    => 'auto_active',
            ), 
              
            'redirection'   => array(
                'login'     => array(
                    'administrator' => 'dashboard',
                    'subscriber'    => 'default',
                ),
                'logout'    => array(
                    'administrator' => 'default',
                    'subscriber'    => 'default',
                ),
                'registration'  => array(
                    'administrator' => 'default',
                    'subscriber'    => 'default',
                ),                
            ),
            
            'backend_profile'   => array(), 
            
            'misc'  => array(),
            
                
        );
        
        if( $key )
            return @$settings[ $key ];
        return $settings;        
    }
    
    function defaultEmailsArray( $key = null ){
        global $userMeta;

        $emails = array(
        
            'registration'  => array(
                'user_email'    => array(
                    'subject'   => '[%site_title%] Your username and password',
                    'body'      => "Username: %user_login% \r\nE-mail: %user_email% \r\nPassword: %password% \r\n\r\nLogin Url: %login_url%",

                ),
                'admin_email'    => array(
                    'subject'   => '[%site_title%] New User Registration',
                    'body'      => "Username: %user_login% \r\nEmail: %user_email% \r\n",
                ),                
            ),
            
            'activation'  => array(
                'user_email'    => array(
                    'subject'   => '[%site_title%] User Activated',
                    'body'      => "Congratulations! \r\n\r\nYour account is activated. You can login with your username and password. \r\n\r\nLogin Url: %login_url%",
                ),               
            ),
            
            'deactivation'  => array(
                'user_email'    => array(
                    'subject'   => '[%site_title%] User Deactivated',
                    'body'      => "Your account is deactivated by administrator. You can not login anymore to [%site_title%].",
                ),               
            ),
            
            'email_verification'  => array(
                'user_email'    => array(
                    'subject'   => '[%site_title%] Email verified',
                    'body'      => "Your email [%user_email%] is successfully verified on [%site_title%].",
                ),  
                'admin_email'    => array(
                    'subject'   => '[%site_title%] Email verified',
                    'body'      => "User email [%user_email%] is successfully verified on [%site_title%].",
                ),                                                
            ),                                    
            
            'lostpassword'  => array(
                'user_email'    => array(
                    'subject'   => "[%site_title%] Password Reset",
                    'body'      => "Someone requested that the password be reset for the following account:\r\n\r\n%site_url% \r\n\r\nUsername: %user_login% \r\n\r\nIf this was a mistake, just ignore this email and nothing will happen. \r\n\r\nTo reset your password, visit the following address: \r\n\r\n%reset_password_link% \r\n",
                ),                
            ),            
            
        );
        
        if( $key )
            return @$emails[ $key ];
        return $emails;         
    }
    
}
endif;
