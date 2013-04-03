<?php
global $userMeta;

$html = "<p style=\"color:red\"><strong>" . sprintf( __( 'This feature is only supported in Pro version. Get %s', $userMeta->name ), "<a href='{$userMeta->website}'>User Meta Pro</a>" ) . "</strong></p>";
?>

<div class="wrap">
    <div id="icon-users" class="icon32 icon32-posts-page"><br /></div>  
    <h2><?php _e( 'Export & Import', $userMeta->name ); ?></h2>    
    <div id="dashboard-widgets-wrap">
        <?php echo $html; ?>
        <img src="<?php echo $userMeta->website; ?>/wp-content/images/export-import.jpg" width="100%" onclick="umGetProMessage(this)" />
    </div>
</div>