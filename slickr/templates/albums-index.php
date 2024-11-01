<?php

/* This template lists out your Flickr albums, and forms the gallery navigation */

/* set some variables */
//$highlight = $flickr->getPhotoSizes($album['primary']);
/*
$Slickr_flickr_max_albums = get_option('Slickr_flickr_max_albums');
$totalalbumpages = ceil(count($albums) / $Slickr_flickr_max_albums);
$albumpagenum = 1;

if(count($albums) < $Slickr_flickr_max_albums) {
    $totalalbumpages = 1;
}
if(isset($_GET['page'])) {
    $albumpagenum = $_GET['page'];
}
$albums = array_slice($albums, (($Slickr_flickr_max_albums * $albumpagenum) - $Slickr_flickr_max_albums), $Slickr_flickr_max_albums);          
*/

echo("
<div id=\"slickr\">
    <div id=\"slickrajaxmenufloat\">
        <div id=\"slickrajaxmenu\" style=\"opacity: 0.001\">\n");
        
        include(dirname(__FILE__).'/galleries_menu.php');
        
        echo("
        </div>
    </div>
    <div id=\"slickrajaxcontent\">
    <p class=\"slickr_heading\">\n");
    echo(_("Loading Flickr.com content..."));
    
    $myspinner = get_settings('siteurl')."/wp-content/plugins/slickr-gallery/themes/".urldecode(get_option('Slickr_theme'))."/loading.gif";
    
    echo("\n</p>\n<img src=\"".($myspinner)."\" alt=\"loading\" style=\"border:none; padding-top: 10px\" />
    </div>
</div>
<div style=\"clear: both;\"></div>");
?>