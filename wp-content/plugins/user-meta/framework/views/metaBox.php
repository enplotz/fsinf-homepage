<?php
global $pfInstance;

$deleteLink = $deleteIcon ? "<div class='pf_trash' title='" . __( 'Click to Remove' , $pfInstance->name ) . "' onclick='pfRemoveMetaBox(this);'></div>" : null;
$display    = !$isOpen ? "style='display:none'" : "";


$html = "
<div id='side-sortables' class='meta-box-sortables'>
    <div id='user_meta' class='postbox '>
        $deleteLink
        <div class='handlediv' title='" . __( 'Click to toggle', $pfInstance->name ) . "' onclick='pfToggleMetaBox(this);'><br></div>
        <h3 class='hndle'>$title</h3>
        <div class='inside' $display>
            <p></p>
            $content
            <p></p>
        </div>
    </div>
</div> 
";
?>