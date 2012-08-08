<?php

if( !class_exists( 'umSupportModel' ) ) :
class umSupportModel {    
                       
    /**
     * Get um fields by 
     * @param $by: key, field_group
     * @param $value: 
     */    
    function getFields( $by=null, $param=null, $get=null, $isFree=false ){
        global $userMeta;
        $fieldsList = $userMeta->umFields();
        
        if( !$by )
            return $fieldsList;
        
        //$result = array();
        if( $param ){
            if( $by == 'key' ){
                if( key_exists( $param, $fieldsList ) )
                    return $fieldsList[$param];
            }else{
                foreach( $fieldsList as $key => $fieldData ){
                    if( $fieldData[$by] == $param ){
                        if( $isFree ){
                            if( !$fieldData['is_free'] ) continue;
                        }
                                                
                        if( !$get )
                            $result[$key] = $fieldData;
                        else    
                            $result[$key] = $fieldData[$get];
                    }
                }
            }
        }      
        
        return isset( $result ) ? $result : false;
    }    
    
    
    /**
     * Extract fielddata from fieldID
     * @param $fieldID
     * @param $fieldData : if $fieldData not set the it will search for field option for fielddata
     * @return array: field Data
     */
    function getFieldData( $fieldID, $fieldData=null ){
        global $userMeta;       
        
        if( !$fieldData ){
            $fields = $userMeta->getData( 'fields' );
            if( !isset($fields[$fieldID]) ) return;
            $fieldData = $fields[$fieldID];
        }
        
        //Setting Field Group
        $field_type_data   = $userMeta->getFields( 'key', $fieldData['field_type'] );
        $field_group        = $field_type_data['field_group'];                
                
        //Setting Field Name
        $fieldName = null;
        if( $field_group == 'wp_default' ){
            $fieldName = $fieldData['field_type'];
        }else{
           if( isset($fields[$fieldID]['meta_key']) )
                $fieldName = $fieldData['meta_key'];
        }              
        
        // Check if field is readonly
        $readOnly = @$fieldData['read_only'];
        if( !@$readOnly && @$fieldData['read_only_non_admin'] )
            $readOnly = $userMeta->isAdmin() ? false : true;          
        
        $returnData = $fieldData;
        $returnData['field_id']     = $fieldID;
        $returnData['field_group']  = $field_group;
        $returnData['field_name']   = $fieldName;
        $returnData['meta_key']     = isset($fieldData['meta_key']) ? $fieldData['meta_key'] : null;
        $returnData['field_title']  = isset($fieldData['field_title']) ? $fieldData['field_title'] : null;
        $returnData['required']     = isset($fieldData['required']) ? true : false;
        $returnData['unique']       = isset($fieldData['unique']) ? true : false;
        $returnData['read_only']    = @$readOnly;
        
        return $returnData;
    }
    
    
    /**
     * Get Custom Fields from 'Fields Editor'.
     * @return array of meta_key if success, false if no meta key.
     */
    function getCustomFields(){
        global $userMeta;
        
        $fields = $userMeta->getData( 'fields' );
        if( !$fields || !is_array( $fields ) ) return false;
        
        foreach( $fields as $field ){
            if( @$field['meta_key'] )
                $metaKeys[] = $field['meta_key'];
        }        
        return @$metaKeys ? $metaKeys : false;
    }
    

    /**
     * Add Custom Fields to 'Fields Editor'.
     * @param array $metaKeys: meta_key array which will be added.
     * @return bool: true if updadated, false if fail.
     */
    function addCustomFields( $metaKeys=array() ){
        global $userMeta;
        if( !$metaKeys || !is_array( $metaKeys ) ) return false;
        
        $fields = $userMeta->getData( 'fields' );      
        $existingKeys = $this->getCustomFields();
            
        foreach( $metaKeys as $meta ){
            if( !$existingKeys )
                $add = true;
            elseif( !in_array( $meta, $existingKeys ) )
                $add = true;
            
            if( @$add ) {
                $fields[] = array(
                    'field_title'       => $meta,
                    'field_type'        => 'text',
                    'title_position'    => 'top',
                    'meta_key'          => $meta
                );                
            } 
            unset( $add );                
        } 
        return $userMeta->updateData( 'fields', $fields );                      
    }

    /**
     * Validate input field from a form
     * @param $form_key
     * @return array: key=field_name 
     */
    function formValidInputField( $form_key ){
        global $userMeta;
        
        $forms  = $userMeta->getData( 'forms' );      
        if( !isset( $forms[ $form_key ][ 'fields' ] ) ) return;
        
        foreach( $forms[ $form_key ][ 'fields' ] as $fieldID ){
            $fieldData  = $this->getFieldData( $fieldID );
            if( $fieldData['field_group'] == 'wp_default' OR $fieldData['field_group'] == 'standard' ){
                if( $fieldData['field_group'] == 'standard' AND !isset($fieldData['meta_key']) ) continue;
                if( @$fieldData['read_only'] ) continue;
                   
                $validField[ $fieldData[ 'field_name' ] ] = $fieldData;
                
                //$validField[ $fieldData[ 'field_name' ] ][ 'field_title' ] = $fieldData[ 'field_title' ];
                //$validField[ $fieldData[ 'field_name' ] ][ 'field_type' ]  = $fieldData[ 'field_type' ];
                //$validField[ $fieldData[ 'field_name' ] ][ 'required' ]    = $fieldData[ 'required' ];
                //$validField[ $fieldData[ 'field_name' ] ][ 'unique' ]      = $fieldData[ 'unique' ];              
            }        
        }
        
        return isset($validField) ? $validField : null;
    }
    
    function removeCache( $cacheType, $cacheValue, $byKey=true ){
        global $userMeta;
        
        $cache  = $userMeta->getData( 'cache' );
        if( isset($cache[$cacheType]) ){            
            if( !is_array( $cacheValue ) )
                $cacheValue = array($cacheValue);
                
            foreach( $cacheValue as $key => $val ){
                $cacheKey = $val;
                if( !$byKey )
                    $cacheKey = array_search( $val, $cache[$cacheType] );   
                unset( $cache[$cacheType][$cacheKey] );             
            }
            $userMeta->updateData( 'cache', $cache );
        }           
    }
    
    function clearCache(){
        global $userMeta;
        $cache  = $userMeta->getData( 'cache' );
        
        unset( $cache[ 'version' ] );
        unset( $cache[ 'version_type' ] );
        unset( $cache[ 'upgrade' ] );
        unset( $cache[ 'image_cache' ] );
        
        $csv_files = $cache[ 'csv_files' ];
        foreach( $csv_files as $key => $val ){
            $time = time() - ( 3600 * 6 );
            if( $key < $time )
                unset( $cache[ 'csv_files' ][ $key ] );
        }
        $userMeta->updateData( 'cache', $cache );
    }
    
    // Sleep
    function isUpgradationNeeded(){
        global $userMeta;
        
        // check upgrade flug
        $cache = $userMeta->getData( 'cache' ); 
        if( isset( $cache['upgrade']['1.0.3']['fields_upgraded'] ) )
            return false;        
           
        // Check data exists in new version
        $fields = $userMeta->getData( 'fields' );
        $exists = false;
        if( $fields ){
            if( is_array($fields) ){
                foreach( $fields as $value ){
                    if( isset($value['field_type']) )
                        $exists = true;
                }
            }
        }
        if($exists) return false;   
        
        $prevDefaultFields  = get_option( 'user_meta_field_checked' ); 
        $prevFields         = get_option( 'user_meta_field' );
        if( $prevDefaultFields or $prevFields )
            return true;             
    }
        
    function ajaxUmCommonRequest(){
        global $userMeta;
        $userMeta->verifyNonce();        
        die();
    }    
    
    function getProfileLink( $pre=null ){
        global $userMeta;
        
        $general = $userMeta->getSettings( 'general' );
        if( @$general[ 'profile_page' ] )
            $link = get_permalink( $general[ 'profile_page' ] );
        else
            $link = admin_url( 'profile.php' ); 
        
        return $link;
    }
                  
    function pluginUpdateUrl(){
        global $userMeta;
        $plugin = $userMeta->pluginSlug;
        $url = wp_nonce_url( "update.php?action=upgrade-plugin&plugin=$plugin", "upgrade-plugin_$plugin" );                
        return $url = admin_url( $url );                                        
    }
    
    function getSettings( $key ){
        global $userMeta;
        
        $settings   = $userMeta->getData( 'settings' );
        $data       = @$settings[ $key ];
        
        if( !$data )
            $data   = $userMeta->defaultSettingsArray( $key );
        
        return $data;
    }
    
    /**
     * Get Email Template. if database is empty then use default data.
     * @param   : string $key
     * @return  : array
     */
    function getEmailsData( $key ){
        global $userMeta;
        
        $data       = $userMeta->getData( 'emails' );
        $emails     = @$data[ $key ];
        
        if( empty( $emails ) ){
            $default    = $userMeta->defaultEmailsArray( $key );  
            $roles      = $userMeta->getRoleList();           
            foreach( $roles as $key => $val ){
                if( empty( $email[ 'user_email' ][ $key ][ 'subject' ] ) )
                    $emails[ 'user_email' ][ $key ][ 'subject' ]    = @$default[ 'user_email' ][ 'subject' ];
                if( empty( $email[ 'user_email' ][ $key ][ 'body' ] ) )
                    $emails[ 'user_email' ][ $key ][ 'body' ]       = @$default[ 'user_email' ][ 'body' ]; 
                      
                if( empty( $email[ 'admin_email' ][ $key ][ 'subject' ] ) )
                    $emails[ 'admin_email' ][ $key ][ 'subject' ]    = @$default[ 'admin_email' ][ 'subject' ];
                if( empty( $email[ 'user_email' ][ $key ][ 'body' ] ) )
                    $emails[ 'admin_email' ][ $key ][ 'body' ]       = @$default[ 'admin_email' ][ 'body' ];                              
            }                       
        }
                              
        return $emails;     
    }

}
endif;
