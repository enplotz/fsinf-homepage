<?php

//require_once( dirname( __FILE__ ) . '/../init.php' );
define('WP_USE_THEMES', false);
require('/var/www/wp/wp-load.php');

class PluginFrameworkRawFunctionTest extends PHPUnit_Framework_TestCase
{
    
    /**
     *
     * @global type $pluginFramework
     * @param type $data 
     */
    public function testCreateInput( $data=false ){
        global $pluginFramework;
        
        //$data = $pluginFramework->createInput();
    }
    
}
?>
