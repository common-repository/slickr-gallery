<?php
/* This page shows up under WP Admin->Options -> Slickr Gallery */
global $Slickr;
global $SlickrPlugin;

if ($error) {
    echo("<div id=\"message\" class=\"error fade\"><p><strong>".$error."</strong></p></div>");
} elseif ($message) {
    echo("<div id=\"message\" class=\"updated fade\"><p><strong>".$message."</strong></p></div>");
}
?>
<div class="wrap">
<h2><?php _e('Slickr Gallery Settings', 'slickr') ?></h2>

<?php
if (get_option('Slickr_flickr_nag') == 1) {
    echo("\n<!-- I hate doing this... -->\n");
    echo("<div  style=\"float:right;width:160px;background:#ccc;border:1px solid #999;padding:10px;font-size:0.9em;margin:10px 0 10px 10px;\">
<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">
<h3>Dig Slickr Gallery?</h3>
<p>This plugin is free software, and will always remain so.</p>
<p>If you like this plugin, and wish to contribute to its development, consider making a donation.</p>
<input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\" />
<input type=\"image\" src=\"https://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif\" name=\"submit\" alt=\"Make payments with PayPal - it's fast, free and secure!\" />
<img alt=\"\" border=\"0\" src=\"https://www.paypal.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\" />
<input type=\"hidden\" name=\"encrypted\" value=\"-----BEGIN PKCS7-----MIIHTwYJKoZIhvcNAQcEoIIHQDCCBzwCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYCcp7jWkyJ2oH1fdCqljQpFMKjt6r78VbDnP4PFOXZf8EL3lfTAXq4v1JXv00OTE05mdz2glywOgSmtP5zKMO/nBkcdsgUzbIy3lqsvyYN+AyWK4Fq6rVzrsssJUmLNKkk6MEvVO61LV3wXw1szd/Ah4IrV6duEsBsqD6P222l25TELMAkGBSsOAwIaBQAwgcwGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIfrp9ZAeqOI2AgagMP1TysomzYGlC2Ka3JQtdknec/XT/VPH4qAJBdDfdzg4h1/lKnNphY8ESjxOnyDbPndVzHQLU4+I9ItV93TZcjZ4VOGhfWuksQydV62IBfoUYKNmmIUFTLhpwxhIk1Ld2ak6YpWwnXcVdCXX/Nsz/8uh83eCSb1wAxjBe0NSO3KJMA5DJZEb8qgFs5oEWrSbEyIEyzTEDp5JaFh62rBQLbvzZZ3DUWG+gggOHMIIDgzCCAuygAwIBAgIBADANBgkqhkiG9w0BAQUFADCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wHhcNMDQwMjEzMTAxMzE1WhcNMzUwMjEzMTAxMzE1WjCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAMFHTt38RMxLXJyO2SmS+Ndl72T7oKJ4u4uw+6awntALWh03PewmIJuzbALScsTS4sZoS1fKciBGoh11gIfHzylvkdNe/hJl66/RGqrj5rFb08sAABNTzDTiqqNpJeBsYs/c2aiGozptX2RlnBktH+SUNpAajW724Nv2Wvhif6sFAgMBAAGjge4wgeswHQYDVR0OBBYEFJaffLvGbxe9WT9S1wob7BDWZJRrMIG7BgNVHSMEgbMwgbCAFJaffLvGbxe9WT9S1wob7BDWZJRroYGUpIGRMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbYIBADAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBQUAA4GBAIFfOlaagFrl71+jq6OKidbWFSE+Q4FqROvdgIONth+8kSK//Y/4ihuE4Ymvzn5ceE3S/iBSQQMjyvb+s2TWbQYDwcp129OPIbD9epdr4tJOUNiSojw7BHwYRiPh58S1xGlFgHFXwrEBb3dgNbMUa+u4qectsMAXpVHnD9wIyfmHMYIBmjCCAZYCAQEwgZQwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tAgEAMAkGBSsOAwIaBQCgXTAYBgkqhkiG9w0BCQMxCwYJKoZIhvcNAQcBMBwGCSqGSIb3DQEJBTEPFw0wNzExMDYxMTMyMzBaMCMGCSqGSIb3DQEJBDEWBBRpljx+2+Mdq2CViWLO/fuAZMguBTANBgkqhkiG9w0BAQEFAASBgB8MtN3VH6Qa6c2r7K/OPDEmqESjqydtu9qP1vB7zQiS+VyQgiJcDFmzG2QACvcbLNi2eLi7/RC5FjE2RJhVQGAKkJ8nYXfGiRIcqh7EMzHniYR9pcPmhpj3uohze//hwnCqVp72ITR9QgYorH4jxyn/+clcL4U/+hwiqcEjiBCr-----END PKCS7-----
\" />
</form>
</div>");
}

echo("
<p>This plugin will retrieve your Flickr photosets and display them as albums on a page within this site. It also allows you to easily add your photos to your blog posts or pages. To insert photos into your posts, just click the <em>Photos</em> tab while <a href=\"".get_settings('siteurl')."/wp-admin/post.php\" title=\"create a new post\">editing a post</a> and select a thumbnail.</p>

<h3>Before you start</h3> 
<ul>
<li>Slickr requires a working <a href=\"http://www.stimuli.ca/lightbox/\" title=\"get Lightbox\">Lightbox plugin</a> to function properly.</li>
<li>After you have updated a Flickr set, or created a new one, visit this page to refresh Slickr&apos;s cache to show your latest photos.</li>
<li>This plugin is provided &apos;as is&apos;, meaning it is <em>unsupported</em> and comes with no official technical support. <br />For unofficial technical support, as well as updated versions of Slickr, visit <a href=\"http://www.stimuli.ca/slickr/\">www.stimuli.ca/slickr/</a>. You can subscribe to <a rel=\"alternate\" type=\"application/rss+xml\" title=\"my RSS feed\" href=\"http://www.stimuli.ca/feed/\">my RSS feeds</a> to be notified of new versions.</li>
</ul>");

if (!$flickrAuth) {
    $redirect = $_SERVER['SCRIPT_URI'] . '?' . $_SERVER['QUERY_STRING'];
    $perms = "read";
    $api_sig = md5($flickr->getSharedSecret() . 
        "api_key" . $flickr->getAPIKey() . 
        "frob"  . $frob .
        "perms" . $perms);

echo("
<h3>Flickr Settings</h3>

<p>Slickr is currently not linked to your Flickr account. Let&apos;s get started!</p>
<h3>Step 1</h3>
<p>Login to Flickr and grant <em>read only</em> permissions to this photo album.
Once you are done, close the popup window and click the button in Step 2.
</p>

<form method=\"get\" action=\"http://flickr.com/services/auth/\" target=\"_blank\">
<input type=\"hidden\" name=\"api_key\" value=\"".$flickr->getAPIKey()."\" />
<input type=\"hidden\" name=\"frob\" value=\"".$frob."\" />
<input type=\"hidden\" name=\"perms\" value=\"".$perms."\" />
<input type=\"hidden\" name=\"api_sig\" value=\"".$api_sig."\" />
<input type=\"submit\" value=\"Retrieve Flickr Permissions\" />
</form>

<h3>Step 2</h3>    
<p>Apply the permissions granted in Step 1 to this photo album. This step may take a minute to complete, since it&apos;s also going to grab your Flickr information.</p>
    
<form method=\"post\" id=\"flickr\" action=\"\">
<input type=\"hidden\" name=\"action\" value=\"save\" />
<input type=\"hidden\" name=\"frob\" value=\"".$frob."\" />
<input type=\"submit\" value=\"Apply Permissions\" />
</form>

<p>
<strong>Note</strong> You can revoke the permissions granted here in <a href=\"http://flickr.com/services/auth/list.gne\">your Flickr access control panel</a>.
</p>");


} else {

echo("
<h3>Flickr Settings</h3>
<table class=\"form-table\">
<tr valign=\"baseline\">
<th scope=\"row\">Cached Recent Photos</th> 
<td>");

global $SlickrPlugin; 
$myphotos = $SlickrPlugin->getSlickrRecentPhotos();
foreach ($myphotos as $photo) {
    echo("
            <img src=\"".($photo['sizes']['Square']['source'])."\" width=\"".($photo['sizes']['Square']['width']/2)."\" height=\"".($photo['sizes']['Square']['height']/2)."\" alt=\"".(htmlspecialchars($photo['title'], ENT_QUOTES))."\" style=\"border: 1px solid #000;\" />");
}

echo("
<p><small>Your Flickr albums are cached locally to speed things up. Refresh your albums to show more recent additions.</small></p>
<form method=\"post\" action=\"\">
<input type=\"hidden\" name=\"action\" value=\"clearcache\" />
<input type=\"hidden\" name=\"album\" value=\"all\" />
<p class=\"submit\"><input type=\"submit\" value=\"Refresh Albums\" /></p>
</form>
</td>
</tr>

<tr valign=\"baseline\">
<th scope=\"row\">Flickr Account</th> 
<td>
<form method=\"post\" action=\"\">
<input type=\"hidden\" name=\"action\" value=\"logout\" />
<p><a href=\"http://flickr.com/photos/". $user['user']['nsid']."/\"><strong>". $user['user']['username']."</strong></a></p>
<p><small>Slickr Gallery&apos;s access is <em>read only</em></small></p>
<p class=\"submit\"><input type=\"submit\" value=\"Remove Link\" /></p> 
</form>
</td>
</tr>
</table>

<h3>Slickr Gallery Settings</h3>");
if ($baseurl) {
	echo("<p>View <a href=\"". $baseurl."\" title=\"". $baseurl."\" >your gallery</a></p>");
}
echo("
<form method=\"post\" action=\"\">
<input type=\"hidden\" name=\"action\" value=\"galleryoptions\" />
<table class=\"form-table\">
<tr valign=\"baseline\">
<th scope=\"row\">Slickr Gallery URL</th> 
<td>
".bloginfo('siteurl')."/
<input type=\"text\" name=\"baseurl\" value=\"".substr($baseurl, strlen($baseurl_pre))."\" />

<p><small>Enter the path where you want your photo album to be shown</small></p>");

echo("
</td>
</tr>

<tr valign=\"baseline\">
<th scope=\"row\">Show Albums</th> 
<td>");

/* Our Album selection checkboxes: */
$albums = $flickr->getAlbums();
$showme = get_option('Slickr_showalbum');
//print_r($_POST);
//print_r($showme);
echo("<ul>");
foreach($albums as $album) {
    echo("<li><input type=\"checkbox\" name=\"Slickrshowme[]\" value=\"".$album['id']."\" ");
    foreach($showme as $me) {
        if($me == $album['id']) {
            echo("checked=\"checked\" ");
        }
    }
    echo("/> ".htmlspecialchars($album['title'], ENT_QUOTES)."</li>");
}
echo("\n</ul>

<p><small>De-select albums you don&apos;t want to show</small></p>
</td>
</tr>

<tr valign=\"baseline\">
<th scope=\"row\">Albums Per Page</th> 
<td>
<input type=\"text\" name=\"Slickr_flickr_max_albums\" maxlength=\"2\" size=\"2\" value=\"".(get_option('Slickr_flickr_max_albums'))."\" />
<p><small>Number of Flickr albums to show at once in the gallery menu</small></p>
</td>
</tr>

<tr valign=\"baseline\">
<th scope=\"row\">Photos Per Page</th> 
<td>
<input type=\"text\" name=\"Slickr_flickr_max_photos\" maxlength=\"2\" size=\"2\" value=\"".(get_option('Slickr_flickr_max_photos'))."\" />
<p><small>Number of Flickr photo thumbnails to show at once in the thumbnail gallery</small></p>
</td>
</tr>


<tr valign=\"baseline\">
<th scope=\"row\">Gallery Thumbnail Size</th> 
<td>
<input type=\"text\" id=\"thumbsize\" name=\"Slickr_thumb_size\" maxlength=\"2\" size=\"2\" value=\"".(get_option('Slickr_thumb_size'))."\" />
<div id=\"track4\" style=\"background:#CCC; width:140px; height:5px; margin-top:10px;\">
<div id=\"handle4\" style=\"width:10px; height:3px; background:#f00; cursor:move; border:1px solid #000;\"> </div></div>
<p><img id=\"examplethumb\" src=\"".($photo['sizes']['Square']['source'])."\" width=\"".get_option('Slickr_thumb_size')."\" height=\"".get_option('Slickr_thumb_size')."\" alt=\"".(htmlspecialchars($photo['title'], ENT_QUOTES))."\" style=\"border: 1px solid #000;\" /></p>
<script type=\"text/javascript\" language=\"javascript\">
// <![CDATA[
new Control.Slider('handle4','track4',{
    range:\$R(5,75),
    sliderValue:".get_option('Slickr_thumb_size').",
    values:[5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75],
    onSlide:function(v){
        \$('thumbsize').value=v; 
        \$('examplethumb').style.width=(v)+'px'; 
        \$('examplethumb').style.height=(v)+'px';
    }
});
// ]]>
</script>
<p><small>Set the size of Flickr photo thumbnails</small></p>
<p>Show sample pictures with the album names: <input type=\"checkbox\" name=\"Slickr_album_icon_image\" value=\"1\" ".($Slickr_album_icon_image ? 'checked="checked"' : '')." id=\"Slickr_album_icon_image\" /></p>
</td>
</tr>


<tr valign=\"baseline\">
<th scope=\"row\">Slickr Gallery Theme</th> 
<td>");

/* Check if there are themes: */
$Slickr_themes_path =  get_option('Slickr_themes_path');
//print_r($Slickr_themes_path);
if ($handle = opendir($Slickr_themes_path)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && $file != ".DS_Store") {
            $theme_dirs[$file] = $Slickr_themes_path."/".$file."/";
        }   
    }
    closedir($handle);
}
//print_r($theme_dirs);

/* Create a drop-down menu of the valid themes: */
echo("\n<select name=\"Slickr_theme\">\n");
$current_theme = get_option('Slickr_theme');
foreach($theme_dirs as $shortname => $fullpath) {
    if((file_exists($fullpath."/slickr.css")) && (file_exists($fullpath."/view.php"))) {
        if($current_theme == urlencode($shortname)) {
            echo("<option value=\"".urlencode($shortname)."\" selected=\"selected\">".$shortname."</option>\n");
        } else {
            echo("<option value=\"".urlencode($shortname)."\">".$shortname."</option>\n");
  
        }
    }
}
echo("</select>

<p><small>If in doubt, try the <em>Default</em> theme</small></p>
</td>
</tr>

<tr valign=\"baseline\">
<th scope=\"row\">Widget</th> 
<td>
Show 
<input type=\"text\" name=\"Slickr_widget_number_of_photos\" maxlength=\"2\" size=\"2\" value=\"".(get_option('Slickr_widget_number_of_photos'))."\" /> recent photos in the widget. Randomize the photos:
<input type=\"checkbox\" name=\"Slickr_widget_randomize\" value=\"1\" ".($Slickr_widget_randomize ? 'checked="checked"' : '')." id=\"randomize\" />
<p><small>These settings apply to the Hemingway Block as well</small></p>
</td>
</tr>

</table>

<p class=\"submit\"><input type=\"submit\" value=\"save settings\" /></p>

</form>");

}
echo("\n</div>");
?>