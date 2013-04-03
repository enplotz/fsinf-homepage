<?php
global $userMeta;
// Expect: $filepath, $field_name, $avatar, $width, $height, $crop, $readonly

$html = null;

// If avatar
if( @$avatar ) :
    $html .= $avatar;

// Showing Uploaded file
elseif( @$filepath ) :
    $uploads    = wp_upload_dir();
    $path       = $uploads['basedir'] . $filepath;
    $fullUrl    = $uploads['baseurl'] . $filepath;
    
    $fileData   = pathinfo( $path );
    $fileName   = $fileData['basename'];

    if( !file_exists( $path ) ) return;               

    // In case of image
    if( is_array( getimagesize( $fullUrl ) ) ){
        if( !empty( $width ) && !empty( $height ) ){
            
            /**
             * image_resize is depreated from version 3.5 
             */
            if( version_compare( get_bloginfo('version'), '3.5', '>=' ) ){
                $image = wp_get_image_editor( $path );
                if ( ! is_wp_error( $image ) ) {
                    $image->resize( $width, $height, $crop );
                    $image->save( $path );
                }                
            }else{
                $resizedImage = image_resize( $path, $width, $height, $crop );
                if( !is_wp_error($resizedImage) )
                    $path = $resizedImage;               
            }     
            
            $fullUrl    = str_replace( $uploads['basedir'], $uploads['baseurl'], $path );
            $filepath   = str_replace( $uploads['basedir'], '', $path );            
            
            
            /*$resizedImage = image_resize( $path, $width, $height, $crop );
            if( ! is_wp_error($resizedImage) ){
                $fullUrl    = str_replace( $uploads['basedir'], $uploads['baseurl'], $resizedImage );
                $filepath   = str_replace( $uploads['basedir'], '', $resizedImage );
            }else{
                //$html .= $userMeta->showError( $resizedImage->get_error_message() );
            }*/
        }        
        $html.= "<img src='$fullUrl' alt='$fileName' title='$fileName' />";  
    }else
        $html.= "<a href='$fullUrl'>$fileName</a>";           
endif;

// Remove Link
if( (@$avatar OR @$filepath) AND !@$readonly )
    $html .= "<p><a href='#' onclick='umRemoveFile(this)' name='$field_name'>Remove</a><p>";

// Hidden field
if( @$field_name AND !@$readonly )
    $html.= "<input type='hidden' name='$field_name' value='$filepath' />";
            
?>