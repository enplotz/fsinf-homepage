<?php

//require_once( dirname( __FILE__ ) . '/../init.php' );
define('WP_USE_THEMES', false);
require('/var/www/wp/wp-load.php');

class PluginFrameworkArrayTest extends PHPUnit_Framework_TestCase
{
    
    public function testSettingsArray(){
        global $pluginFramework;
        
        $data = $pluginFramework->settingsArray();
        
        $this->assertTrue( is_array( $data ) );
        $this->assertNotEmpty( $data );
        $this->assertArrayHasKey( 'nonce', $data );
        $this->assertTrue(true);
    }
    
    /**
     * TODO: Should return for unknown key
     * @global type $pluginFramework 
     */
    public function testDefaultUserFieldsArray(){
        global $pluginFramework;
       
        $data = $pluginFramework->defaultUserFieldsArray();
        
        $this->assertTrue( is_array( $data ) );
        $this->assertNotEmpty( $data );

        $data = $pluginFramework->defaultUserFieldsArray( 'user_pass' );
        $this->assertEquals( __( 'Password', $pluginFramework->name ), $data );        
    }
    
    public function testCountryArray(){
        global $pluginFramework;
       
        $data = $pluginFramework->countryArray();        
        $this->assertTrue( is_array( $data ) );
        $this->assertNotEmpty( $data );        
        
        $data = $pluginFramework->countryArray( 'BD' );  
        $this->assertEquals( 'Bangladesh', $data );
    }
    
}

?>
