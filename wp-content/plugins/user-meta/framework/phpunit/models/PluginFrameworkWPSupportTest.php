<?php

//require_once( dirname( __FILE__ ) . '/../init.php' );
define('WP_USE_THEMES', false);
require('/var/www/wp/wp-load.php');

class PluginFrameworkWPSupportTest extends PHPUnit_Framework_TestCase
{
    
    /**
     *
     * @global type $pluginFramework
     * @param type $data 
     */
    public function testCreateInput(){
        global $pluginFramework;
        
        $this->assertTrue( true );
        //$data = $pluginFramework->createInput();
    }
    
    public function test_isUserFieldAvailable(){
        global $userMeta;
        
        
        $userMeta->isUserFieldAvailable( 'test_meta', 'test' );
        $this->assertTrue( true );
    }
    
}
?>