<?php
global $userMeta; 
// Expected: $settings, $forms, $fields, $default
?>

<div class="wrap">
    <?php screen_icon( 'options-general' ); ?>
    <h2><?php _e( 'User Meta Settings', $userMeta->name ); ?></h2>   
    <?php do_action( 'um_admin_notice' ); ?>
    <div id="dashboard-widgets-wrap">
        <div class="metabox-holder">
            <div id="um_admin_content">
                <?php $userMeta->renderPro( "activationForm", null, "settings" ); ?>
                <?php //echo __( 'User Meta Test' ); ?>
            
                <form id="um_settings_form" action="" method="post" onsubmit="umUpdateSettings(this); return false;" >
                <div id="um_settings_tab">
                	<ul>
                		<li><a href="#um_settings_general"><?php _e( 'General', $userMeta->name ); ?></a></li>     
                		<li><a href="#um_settings_login"><?php _e( 'Login', $userMeta->name ); ?></a></li>
                        <li><a href="#um_settings_registration"><?php _e( 'Registration', $userMeta->name ); ?></a></li>
                        <li><a href="#um_settings_redirection"><?php _e( 'Redirection', $userMeta->name ); ?></a></li>
                        <li><a href="#um_settings_backend_profile"><?php _e( 'Backend Profile', $userMeta->name ); ?></a></li>
                        <!--<li><a href="#um_settings_misc"><?php //_e( 'Misc', $userMeta->name ); ?></a></li>-->
                	</ul>  
                                                               
                    <div id="um_settings_general">
                        <?php
                        echo $userMeta->renderPro( "generalSettings", array(
                            'general' => isset( $settings[ 'general' ] ) ? $settings[ 'general' ] : $default[ 'general' ],
                        ), "settings" );  
                         
                        echo $userMeta->renderPro( "generalProSettings", array(
                            'general' => isset( $settings[ 'general' ] ) ? $settings[ 'general' ] : $default[ 'general' ],
                        ), "settings" );                                                  
                        ?>                        
                    </div> 
                    
                    <div id="um_settings_login">
                        <?php
                        echo $userMeta->renderPro( "loginSettings", array(
                            'login' => isset( $settings[ 'login' ] ) ? $settings[ 'login' ] : $default[ 'login' ],
                        ), "settings" );                            
                        ?>
                    </div> 
                    
                    <div id="um_settings_registration">
                        <?php
                        echo $userMeta->renderPro( "registrationSettings", array(
                            'registration' => isset( $settings[ 'registration' ] ) ? $settings[ 'registration' ] : $default[ 'registration' ],
                        ), "settings" );                            
                        ?>
                    </div>                     
                    
                    <div id="um_settings_redirection">
                        <?php
                        echo $userMeta->renderPro( "redirectionSettings", array(
                            'redirection' => isset( $settings[ 'redirection' ] ) ? $settings[ 'redirection' ] : $default[ 'redirection' ],
                        ), "settings" );                               
                        ?>
                    </div> 
                        
                    <div id="um_settings_backend_profile">
                        <?php
                        echo $userMeta->renderPro( "backendProfile", array(
                            'backend_profile'   => isset( $settings[ 'backend_profile' ] ) ? $settings[ 'backend_profile' ] : $default[ 'backend_profile' ],
                            'forms'             => $forms,
                            'fields'            => $fields
                        ), "settings" );                            
                        ?>
                    </div> 
                    
                    <!--<div id="um_settings_misc">
                        <?php
                        /*echo $userMeta->renderPro( "miscSettings", array(
                            'misc'   => isset( $settings[ 'misc' ] ) ? $settings[ 'misc' ] : $default[ 'misc' ],
                        ), "settings" );  */                          
                        ?>
                    </div>-->                      
                                                                                                          
                </div>
                
                <?php
                echo $userMeta->createInput( "save_field", "submit", array(
                    "value" => __( "Save Changes", $userMeta->name ),
                    "id"    => "update_settings",
                    "class" => "button-primary",
                    "enclose"   => "p",
                ) );                    
                ?>                
                
                </form>
 
           
            
        
            
            
                
                
                <?php                
                /*$userMeta->renderPro( "settings", $settings, "settings" );
                
                $userMeta->renderPro( "loginSettings", array(
                    'login' => isset( $settings[ 'login' ] ) ? $settings[ 'login' ] : $default[ 'login' ],
                ), "settings" );
                
                $userMeta->renderPro( "redirection", array(
                    'redirection' => isset( $settings[ 'redirection' ] ) ? $settings[ 'redirection' ] : $default[ 'redirection' ],
                ), "settings" );                
                
                $userMeta->renderPro( "backendProfile", array(
                    'settings'  => $settings,
                    'forms'     => $forms,
                    'fields'    => $fields
                ), "settings" ); 
                                

                echo $userMeta->createInput( "save_field", "submit", array(
                    "value" => "Save Changes",
                    "id"    => "update_settings",
                    "class" => "button-primary",
                    "enclose"   => "p",
                ) );   */                            
                ?>
                
                </form>
            </div>
            
            <div id="um_admin_sidebar">                            
                <?php
                echo $userMeta->metaBox( __( '3 steps to getting started', $userMeta->name ),  $userMeta->boxHowToUse());               
                if( !@$userMeta->isPro )
                    echo $userMeta->metaBox( __( 'User Meta Pro', $userMeta->name ),   $userMeta->boxGetPro());
                echo $userMeta->metaBox( __( 'Shortcode', $userMeta->name ),   $userMeta->boxShortcodesDocs());
                ?>
            </div>
        </div>
    </div>     
</div>


<script>
jQuery(function() {
    jQuery('.um_dropme').sortable({
        connectWith: '.um_dropme',
        cursor: 'pointer'
    }).droppable({
        accept: '.button',
        activeClass: 'um_highlight',
    });   
    //jQuery( "#accordion" ).accordion();  
    jQuery( "#um_settings_tab" ).tabs();
    
});
</script>   