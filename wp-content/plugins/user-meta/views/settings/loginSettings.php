<?php
global $userMeta;

$html = "<p style=\"color:red\"><strong>" . sprintf( __( 'This feature is only supported in Pro version. Get %s', $userMeta->name ), "<a href='{$userMeta->website}'>User Meta Pro</a>" ) . "</strong></p>";

$html .= "<img src=\"{$userMeta->website}/wp-content/images/settings-login.jpg\" width=\"100%\" onclick=\"umGetProMessage(this)\" />";
?>