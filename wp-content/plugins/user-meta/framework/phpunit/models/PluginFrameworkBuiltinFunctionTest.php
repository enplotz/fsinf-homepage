<?php

//require_once( dirname( __FILE__ ) . '/../init.php' );
define('WP_USE_THEMES', false);
require('/var/www/wp/wp-load.php');

class PluginFrameworkBuiltinFunctionTest extends PHPUnit_Framework_TestCase
{
    
    public function test_retrieve_password(){
        global $pluginFramework;
        
        $_POST = array();
        
        /**
         * If not data comes by $_POST 
         */
        $data = $pluginFramework->retrieve_password();
        $this->assertTrue( is_wp_error( $data ) );
        $this->assertContains( 'empty_username', $data->get_error_codes() );
          
        /**
         * Test with invalid data 
         */
        $_POST[ 'user_login' ] = 'unknown_user';     
        $data = $pluginFramework->retrieve_password();
        $this->assertTrue( is_wp_error( $data ) ); 
        $this->assertContains( 'invalidcombo', $data->get_error_codes() );
               
        /**
         * Check this method return true if not fall to error
         */
        $_POST[ 'user_login' ]  = 'admin';   
        $_SERVER['SERVER_NAME'] = 'localhost';
        $data = $pluginFramework->retrieve_password();
        if( !is_wp_error( $data ) )
            $this->assertTrue( $data );

    }
    
}

?>
