<?php
/* This Hemingway block was mostly written by Sebastian Januszevski 
 ( http://www.fotosdecalle.com.ar ) with minor tweaking by Rupert Morris.
 
You must have the Slickr Gallery 
plugin installed and configured to use this block!  */
 
global $SlickrPlugin;

if (is_object($SlickrPlugin)) {
    $randomize = get_option('Slickr_widget_randomize');
    $tags = ''; /* search ONLY for pics with these tags, leave blank to include all pics */
    $everyone = false; /* everyone's photos, or just yours? */
    $num = get_option('Slickr_widget_number_of_photos');/* how many photos matching above criteria to be fetched */
    $offset = '0';//get_option('Slickr_widget_offset'); /* recentness of first pic in our array; ie: 10 for tenth most recent, 0 for latest uploaded pic */
    
    if($randomize != 0) {
        $random_num = ($num * 4);
        $photos = $SlickrPlugin->getSlickrRecentPhotos($tags, $everyone, $random_num, $offset);
        shuffle($photos); /* randomize the photos for display */
        $photos = array_slice($photos, 0, $num); /* show 9 of the last $num_of_photos... of new photos */
    } else {
        $photos = $SlickrPlugin->getSlickrRecentPhotos($tags, $everyone, $num, $offset);
    }
    
    //print_r($photos);
    //$photos = $SlickrPlugin->getSlickrPhotoSizes($photos);
    
    echo("\n<h2>Photos <a href=\"".get_option('Slickr_flickr_baseurl')."\">(view gallery)</a></h2><div style=\"margin-bottom:25px;\">");
    foreach ($photos as $photo) {
        echo("<a href=\"".$photo['sizes']['Medium']['source']."\" rel=\"lightbox[hemingway_block]\" title=\"".$photo['title']."\"><img class=\"imgblock\" src=\"".$photo['sizes']['Square']['source']."\" width=\"".round($photo['sizes']['Square']['width']*0.75)."\" height=\"".round($photo['sizes']['Square']['height']*0.75)."\" 
            alt=\"".$photo['title']."\" /></a>");
    }
    echo("</div>");
} else {
    echo("<strong>Error:</strong> Your Flickr photo album is not properly configured!</div>");
}
?>