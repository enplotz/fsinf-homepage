<?php
// Expect: $filepath, $field_name, $avatar, $width, $height, $readonly

$html = null;

// If avatar
if( @$avatar ) :
    $html .= $avatar;

// Showing Uploaded file
elseif( @$filepath ) :
    $uploads    = wp_upload_dir();
    $fullPath   = $uploads['basedir'] . $filepath;
    $fullUrl    = $uploads['baseurl'] . $filepath;
    
    $fileData   = pathinfo( $fullPath );
    $fileName   = $fileData['basename'];

    if( !file_exists( $fullPath ) ) return;               

    // In case of image
    if( is_array( getimagesize( "$fullUrl" ) ) ){
        if( @$width AND @$height ){
            $resizedImage = image_resize( $fullPath, $width, $height );
            if( is_wp_error($resizedImage) )
                $error[] = $resizedImage->get_error_message();               
            if( !isset($error) )
                $fullUrl = str_replace( $uploads['basedir'], $uploads['baseurl'], $resizedImage );
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