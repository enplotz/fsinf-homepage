<?php

if( !class_exists( 'umInitPlugin' ) ) :
class umInitPlugin {    
        
    function pluginInit(){
        global $userMeta;
        
        $userMeta->loadControllers( $userMeta->controllersPath );
        $userMeta->loadDirectory( $userMeta->pluginPath . '/helper/' );
        $userMeta->loadDirectory( $userMeta->pluginPath . '/addons/' );
    }
               
}
endif;
