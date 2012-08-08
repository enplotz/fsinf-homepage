<?php
global $userMeta; 
?>

<div class="wrap">
    <?php screen_icon( 'options-general' ); ?>
    <h2><?php _e( 'E-mail Notification', $userMeta->name ); ?></h2>   
    <?php do_action( 'um_admin_notice' ); ?>
    <div id="dashboard-widgets-wrap">
        <div class="metabox-holder">
            <div id="um_admin_content">
                <?php
                    echo $userMeta->metaBox( __( 'Shortcode', $userMeta->name ),   $userMeta->mailForm());
                ?>
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
