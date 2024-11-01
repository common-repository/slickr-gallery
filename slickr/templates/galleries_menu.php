<?php
                    /* set some variables */
                    $thumbsize = get_option('Slickr_thumb_size');
                    $albums = $flickr->getAlbums();
                    $Slickr_showalbum = get_option('Slickr_showalbum');
                    //print_r($Slickr_showalbum);
                    $newalbums = array();
                    /* Drop galleries we don't want to show: */
                    foreach($albums as $album) {
                        foreach($Slickr_showalbum as $showalbum) {
                            if($showalbum == $album['id']) {
                                $newalbums[] = $album;
                            }
                        }
                    }
                    /* recreate $albums with only showable albums: */
                    $albums = $newalbums;
                    
                    $Slickr_flickr_max_albums = get_option('Slickr_flickr_max_albums');
                    $totalalbumpages = ceil(count($albums) / $Slickr_flickr_max_albums);
                    if(count($albums) < $Slickr_flickr_max_albums) {
                        $totalalbumpages = 1;
                    }
                    if(isset($_GET['page'])) {
                        $albumpagenum = $_GET['page'];
                    } else {
                    	$albumpagenum = 1;
                    }
                    
                    $albums = array_slice($albums, (($Slickr_flickr_max_albums * $albumpagenum) - $Slickr_flickr_max_albums), $Slickr_flickr_max_albums);
                    
                    /* Needed for our links to Flickr.com albums: */
                    $user = $flickr->auth_checkToken();
                    $nsid = $user['user']['nsid'];
                    //print_r($user);
                    echo("<p class=\"slickr_heading\">Flickr Albums:</p>");
                    
                    /* Shows our albums menu: */
                    foreach ($albums as $album) {
	                    /// our gallery album icon images:
	                    //print_r($album);
	                    $photo = $flickr->photos_getInfo($album['primary']);
	                    $photo['sizes']['Square']['width'] = '75';
	            		$photo['sizes']['Square']['height'] = '75';
	            		$my_url = ("http://farm".$photo['farm'].".static.flickr.com/".$photo['server']."/".$photo['id']."_".$photo['secret']);
	            		$photo['sizes']['Medium']['source'] = $my_url.".jpg";
	            		$photo['sizes']['Square']['source'] = $my_url."_s.jpg";
	                    ////
                    	//print_r($album_icon_image['1']);
                        echo("\n<div class=\"menu_album\">");
                        if (get_option('Slickr_album_icon_image') == 1) {
                        	echo("<a href=\"album/".$album['id']."/".$album['pagename']."\" rel=\"slickrajaxcontent\" title=\"".$album['title']."\"><img src=\"".$photo['sizes']['Square']['source']."\" alt=\"".$album['title']."\" width=\"".$thumbsize."\" height=\"".$thumbsize."\" /></a>");
                        }
                        echo("<p class=\"gallery_name\">
                        <a href=\"album/".$album['id']."/".$album['pagename']."\" rel=\"slickrajaxcontent\">".htmlspecialchars($album['title'], ENT_QUOTES)."</a>
                        </p>
                    <p class=\"slickr_gallery_description\">".$album['photos']." Photos</p> 
                    <p><a href=\"#\" onclick=\"return openSlideShow('".$album['id']."')\">slideshow</a> | <a href=\"http://www.flickr.com/photos/".$nsid."/sets/".$album['id']."/\">Flickr page</a>
                    </p>
                    </div>");
                    }
                     
                    /*$num_of_favs = 50;
                    $album = $flickr->getAlbum($album['id']); //only getAlbum() returns owner, not getAlbums()!
                    
                    $show_my_favorites = 0; // set to 0 to not display your favorites, or 1 to show them
                    
                    if($show_my_favorites == 1) {
                        $favorites = $flickr->favorites_getList($album['owner'], $extras = NULL, $num_of_favs, $page = 1);
                        if($favorites != 0) { // Do we have favorites on Flickr?
                            echo("
                        <p class=\"gallery_name\"><a href=\"favorites/".$album['owner']."/".$favorites['page']."\" rel=\"slickrajaxcontent\">My Flickr Favorites</a></p>
                        <p class=\"slickr_gallery_description\">");
                            
                            if(($favorites['total'] - $favorites['perpage']) >= 0) {
                                echo($num_of_favs);
                            } else {
                                echo($favorites['total'] - $favorites['perpage']);
                            }
                            echo(" Photos</p> 
                            <p><a href=\"http://www.flickr.com/photos/".$album['owner']."/favorites/\">Flickr page</a></p>");
                        } 
                    } */
                    
                    /* Navigation below albums: */
                    if($totalalbumpages != 1) {
                        echo("<div id=\"slickrnavigation\"><p>");
                        /*echo("\n<p class=\"albumpagelinks\">page:");
                        for($i = 1; $i <= $totalalbumpages; $i++) {
                            echo(" <a href=\"gallerymenu/?page=".$i."\" rel=\"slickrajaxmenu\" title=\"view page ".$i."\">".$i."</a>");
                        }
                        echo("</p>"); */

                        /* We have more pages, so: */
                                                

                        if($albumpagenum < $totalalbumpages) {
                            echo("\n<a href=\"gallerymenu/?page=".($albumpagenum + 1)."\" title=\"view more albums\" rel=\"slickrajaxmenu\">next ".$Slickr_flickr_max_albums." albums &raquo;</a>");
                        }
                        
                        //if(($albumpagenum < $totalalbumpages) && ($albumpagenum > 1)) {
                        //    echo(" | ");
                        //}
                        
                        if($albumpagenum > 1) {
                            echo("\n<a href=\"gallerymenu/?page=".($albumpagenum - 1)."\" title=\"view previous albums\" rel=\"slickrajaxmenu\">&laquo; back</a>");
                        }
                        echo("</p></div>");
                    }
                    
?>
