<?php
/*
Plugin Name: Slickr Gallery
Plugin URI: http://www.stimuli.ca/slickr/
Description: This plugin shows your Flickr photos on your Wordpress site and allows you to easily add your Flickr photos to your posts. It requires a <a href="http://www.stimuli.ca/lightbox" title="get Lightbox">Lightbox plugin</a> to work properly.
Author: Rupert Morris
Version: 0.6.5
Author URI: http://www.stimuli.ca/

Copyright (c) 2007

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

http://www.gnu.org/licenses/gpl.txt

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA

*/
class SlickrPlugin {
        function admin() {
        
        /* To override, and use your own key/shared secret, uncomment these two lines: */
        //$mykey = update_option('SLICKR_FLICKR_APIKEY', "8d7595c95deac1c0ef871a38c751991e");
        //$mysecret = update_option('SLICKR_FLICKR_SHAREDSECRET', "b4090c6189ced220");
                
        /* Get some basics requirements out of the way: */
        $slickr_cache_dir_path = (ABSPATH.'wp-content/plugins/slickr-gallery/slickr/flickr-cache/');
        if(!file_exists($slickr_cache_dir_path)) {
            mkdir(($slickr_cache_dir_path), 0770);
        }
        if (!is_writable($slickr_cache_dir_path)) {
            echo "<div class='wrap'>
            <h2>Permissions Error</h2>
            <p>This plugin requires that the directory <strong>".get_bloginfo('wpurl')."/wp-content/plugins/slickr-gallery/slickr/flickr-cache/</strong> be writable by the web server.</p> 
            <p>Please contact your server administrator to ensure the proper permissions are set for this directory. </p>
            </div>
            ";
            return;
        } elseif (!get_settings('permalink_structure')) {
            $error = "In order to view your photo album, your <a href='options-permalink.php'>WordPress permalinks</a> need to be set to something other than <em>Default</em>.";
        } elseif (!function_exists('curl_init')) {
            $error = "You do not have the required libraries to use this plugin. The PHP library <a href='http://us2.php.net/curl'>libcurl</a> needs to be installed on your server.";
        }
    
        $SLICKR_FLICKR_APIKEY = get_option('SLICKR_FLICKR_APIKEY');
        $SLICKR_FLICKR_SHAREDSECRET = get_option('SLICKR_FLICKR_SHAREDSECRET');
        if((!$SLICKR_FLICKR_APIKEY) || (!$SLICKR_FLICKR_SHAREDSECRET)) {
            $SLICKR_FLICKR_APIKEY = "8d7595c95deac1c0ef871a38c751991e";
            $SLICKR_FLICKR_SHAREDSECRET =  "b4090c6189ced220";
            update_option('SLICKR_FLICKR_APIKEY', $SLICKR_FLICKR_APIKEY);
            update_option('SLICKR_FLICKR_SHAREDSECRET', $SLICKR_FLICKR_SHAREDSECRET);
        }
        
        require_once(dirname(__FILE__).'/slickr/lib.flickr.php');
        
        $flickr = new Slickr();
        if ($flickr->cache == 'db') {
            global $wpdb;
            $wpdb->query("
                CREATE TABLE IF NOT EXISTS `$flickr->cache_table` (
                    `request` CHAR( 35 ) NOT NULL ,
                    `response` TEXT NOT NULL ,
                    `expiration` DATETIME NOT NULL ,
                    INDEX ( `request` )
                ) TYPE = MYISAM");
        }
        
        /* Flickr account options */
        if ($_POST['action'] == 'save') {
            $token = $flickr->auth_getToken($_POST['frob']);
            if ($token) {
                update_option('Slickr_flickr_frob', $_POST['frob']);
                update_option('Slickr_flickr_token', $token);
            } else {
                $error = $flickr->getErrorMsg();
            }
            
        } elseif ($_POST['action'] == 'logout') {
            /* nuke our gallery */
            update_option('Slickr_flickr_token', '');
            $flickr->clearCache();
        } 
        
        /* ----------------------------------------------------------------------------
        Global Variables and Options
        ---------------------------------------------------------------------------- */
        
        $auth_token  = get_option('Slickr_flickr_token');
        $baseurl = get_option('Slickr_flickr_baseurl');
        $baseurl_pre = get_option('Slickr_flickr_baseurl_pre');
        $hideprivate = "1";
        $showbadge   = get_option('Slickr_flickr_showbadge');
        
        /* Added by Rupert 20070205 */
        $Slickrnag = get_option('Slickr_flickr_nag'); // it sucks, I know. Set to "0" to stop.
        $Slickr_widget_number_of_photos = get_option('Slickr_widget_number_of_photos');
        $Slickr_widget_randomize = get_option('Slickr_widget_randomize');
        $Slickr_flickr_max_photos = get_option('Slickr_flickr_max_photos');
        $Slickr_flickr_max_albums = get_option('Slickr_flickr_max_albums');
        $Slickr_showalbum = get_option('Slickr_showalbum');
        $Slickr_theme = get_option('Slickr_theme');
        $Slickr_thumb_size = get_option('Slickr_thumb_size');
        $Slickr_album_icon_image = get_option('Slickr_album_icon_image');
        
        /* ----------------------------------------------------------------------------
        Authentication nonsense
        ---------------------------------------------------------------------------- */
        $flickrAuth = false;
        
        if (!$auth_token) {
            $frob = $flickr->getFrob();
            $flickrAuth = false;
        } else {
            $flickr->setToken($auth_token);
            $flickr->setOption(array(
                'hidePrivatePhotos' => get_option('Slickr_flickr_hideprivate'),
            ));
            $user = $flickr->auth_checkToken();
            if (!$user) { // get a new frob and try to re-authenticate
                $error = $flickr->getErrorMsg();
                update_option('Slickr_flickr_token', '');
                $flickr->setToken('');
                $frob = $flickr->getFrob();
            } else {
                $flickrAuth = true;
                $flickr->setUser($user);
                update_option('Slickr_flickr_user', $user);
            }
        }

        
        /* ----------------------------------------------------------------------------
            Initial (first run) Defaults
            ---------------------------------------------------------------------------- */
        /* sets Widget/Block options defaults: */
        if(!$Slickr_widget_number_of_photos) {
            update_option('Slickr_widget_number_of_photos', '12');
        }
        if(!$Slickr_widget_randomize) {
            update_option('Slickr_widget_randomize', '0');
        }
        if (!$Slickrnag) {
            update_option('Slickr_flickr_nag', "1");
        }
        /* some sane album/gallery page defaults */
        if (!$Slickr_flickr_max_photos) {
            update_option('Slickr_flickr_max_photos', "30");
        }
        if (!$Slickr_flickr_max_albums) {
            update_option('Slickr_flickr_max_albums', "5");
        }
        if (!$Slickr_thumb_size) {
            update_option('Slickr_thumb_size', "55");
        }
        if (!$Slickr_album_icon_image) {
            update_option('Slickr_album_icon_image', "1");
        }
        /* Default to showing all albums */
        if (!$Slickr_showalbum) {
            $Slickr_showalbum = array();
            $albums = $flickr->getAlbums();
            foreach($albums as $album) {
                $Slickr_showalbum[$album['id']] = $album['id'];
            }
            update_option('Slickr_showalbum', $Slickr_showalbum);
        }
        /* Where our themes reside: */
        $Slickr_themes_path = (dirname(__FILE__)."/themes");
        update_option('Slickr_themes_path', $Slickr_themes_path);
        /* Set the theme to Default */
        if (!$Slickr_theme) {
            $Slickr_theme = ('Default');
            update_option('Slickr_theme', $Slickr_theme);
        }

        /* If we've passed authentication: */
        if ($flickrAuth) {
            if ($_POST['action'] == 'clearcache') {
                if ($_POST['album'] == 'all') {
                    if ($flickr->clearCache()) {
                        $message = _("Successfully cleared the cache.");
                    } else {
                        $error = _("Clearing the cache failed. Manually delete the 'flickr-cache' directory.");
                    }
                } else {
                    $flickr->startClearCache();
                    $albums = $flickr->getAlbums();
                    /* Album in this next line refers to all albums */
                    $photos = $flickr->getPhotos($_POST['album']);
                    $flickr->doneClearCache();
                    $message = _("Refreshed " . count($photos) . " photos in ".$albums[$_POST['album']]['title'].".");
                }
            } elseif ($_POST['action'] == 'galleryoptions') {
                /* Gallery related options */
                
                /* wordpress location of gallery */
                $url = parse_url(get_bloginfo('siteurl'));
                $baseurl = $url['path'] . '/' . $_POST['baseurl'];
                if (!ereg('.*/$', $baseurl)) $baseurl .= '/';
    
                if (strlen($_POST['baseurl']) <= 0) {
                    $baseurl = false;
                }
                
                update_option('Slickr_flickr_baseurl_pre', $url['path'] . '/');
                update_option('Slickr_flickr_baseurl', $baseurl);
                update_option('Slickr_flickr_hideprivate', "1");/* If your photos are private, you likely don't want them all over teh intarweb, do you?! */
                update_option('Slickr_flickr_showbadge', $_POST['showbadge']);
                update_option('Slickr_widget_number_of_photos', $_POST['Slickr_widget_number_of_photos']);
                update_option('Slickr_widget_randomize', $_POST['Slickr_widget_randomize']);
                update_option('Slickr_flickr_max_photos', $_POST['Slickr_flickr_max_photos']);
                update_option('Slickr_flickr_max_albums', $_POST['Slickr_flickr_max_albums']);
                update_option('Slickr_theme', $_POST['Slickr_theme']);
                update_option('Slickr_showalbum', $_POST['Slickrshowme']);
                update_option('Slickr_thumb_size', $_POST['Slickr_thumb_size']);
                update_option('Slickr_album_icon_image', $_POST['Slickr_album_icon_image']);
                
            }
        }
        include(dirname(__FILE__).'/slickr/templates/admin-options.php');
    }
    
    /* Our blog post image insertion tools */
    function uploading_iframe($src) {
        return '../wp-content/plugins/slickr-gallery/slickr/templates/'.$src;
    }
    
    /* Added by Rupert for bandwidth conservation and minimal caching: */
    function getSlickrPhotoSizes($photosarray) {
        $myarray = array();
        foreach($photosarray['photo'] as $k => $photo) {
            $photo['sizes']['Square']['width'] = '75';
            $photo['sizes']['Square']['height'] = '75';
            $my_url = ("http://farm".$photo['farm'].".static.flickr.com/".$photo['server']."/".$photo['id']."_".$photo['secret']);
            $photo['sizes']['Medium']['source'] = $my_url.".jpg";
            $photo['sizes']['Square']['source'] = $my_url."_s.jpg";
            
            /* another fix by Ania Krawet: */
            $my_url_org=(" http://farm".$photo['farm'].".static.flickr.com/".$photo['server']."/".$photo['id']."_".$photo['originalsecret']);
            $photo['sizes']['Large']['source'] = $my_url_org."_o.jpg";
            
            $photosarray['photo'][$k]['sizes'] = $photo['sizes'];
        }
        return $photosarray;
    }
    
    function getSlickrPhotos($photoset_id, $per_page, $page) {
        /* http://www.flickr.com/services/api/flickr.photosets.getPhotos.html */
        $flickr = new Slickr();
        $flickr->request("flickr.photosets.getPhotos", array("photoset_id" => $photoset_id, "extras" => "original_format", "privacy_filter" => 1, "per_page" => $per_page, "page" => $page));
        return $flickr->parsed_response ? $flickr->parsed_response['photoset'] : false;
    }
    
    /* the photos that show up in the iframe for blog posts */
    function getRecentPhotos($tags='', $offsetpage=0, $max=15, $everyone=false, $usecache=true) {
        $auth_token = get_option('Slickr_flickr_token');
        $baseurl = get_option('Slickr_flickr_baseurl');
        if ($auth_token) {
            require_once(dirname(__FILE__).'/slickr/lib.flickr.php');
            $flickr = new Slickr();
            $flickr->setToken($auth_token);
            $user = $flickr->auth_checkToken();
            $nsid = $user['user']['nsid'];
            if (!$usecache) $flickr->startClearCache();
            $flickr->_Slickr_cacheExpire = 3600; /* cache for 5 min */
            if (!$tags && $everyone) {
                $photos = $flickr->getRecent(NULL, $max, $offsetpage);
            } else {
                $photos = $flickr->search(array(
                    'tags' => ($tags ? $tags : ''),
                    'user_id' => ($everyone ? '' : $nsid),
                    'per_page' => $max,
                    'page' => $offsetpage,
                    'privacy_filter' => 1,
                ));
            }
            if (!$usecache) $flickr->doneClearCache();
            $this->_Slickr_cacheExpire = -1;
            if ($everyone || !$baseurl) {
                foreach ($photos as $k => $photo) {
                    $photos[$k]['info'] = $flickr->photos_getInfo($photo['id']);
                }
            }
            return $photos;
        } else {
            return array();
        }
    }
    /* Faster option for admin page cached photos and Blocks/Widgets: */
    function getSlickrRecentPhotos($tags = NULL, $user_id = NULL, $per_page = 10, $page = 1) {
        require_once(dirname(__FILE__).'/slickr/lib.flickr.php');
        $auth_token = get_option('Slickr_flickr_token');
        if ($auth_token) {
            require_once(dirname(__FILE__).'/slickr/lib.flickr.php');
            $flickr = new Slickr();
            $flickr->setToken($auth_token);
            $user = $flickr->auth_checkToken();
            $nsid = $user['user']['nsid'];
            $photos = $flickr->search(array(
                'tags' => ($tags ? $tags : ''),
                'user_id' => ($everyone ? '' : $nsid),
                'per_page' => $per_page,
                'page' => $page,
                'privacy_filter' => 1,
            ));
            //$photos = $this->getSlickrPhotoSizes($photos);
        }
        return $photos;
    }

    
    /* redirect to template */
    function template() {
    global $Slickr, $wp_query;
        /* Not really necessary for Slickr: */
        $current = $wp_query->get_queried_object();
        if ($current->post_title) {
            $photoAlbumTitle = $current->post_title;
        } else {
            $photoAlbumTitle = 'Slickr Gallery';
        }
    
        if (isset($_SERVER['_SLICKR_FLICKR_REQUEST_URI'])) {
            $auth_token = get_option('Slickr_flickr_token');
            $photoTemplate = dirname(__FILE__).'/slickr/templates/error.php';
            if ($auth_token) {
                require_once(dirname(__FILE__).'/slickr/lib.flickr.php');
                $flickr = new Slickr();
                $flickr->setToken($auth_token);
                $flickr->setOption(array(
                    'hidePrivatePhotos' => '1',//get_option('Slickr_flickr_hideprivate'),
                ));
                $parts = explode('/', substr($_SERVER['_SLICKR_FLICKR_REQUEST_URI'], strlen($_SERVER['REQUEST_URI'])));
                $request = array();
                $title = '';
                $i = 0;
                if (isset($_POST['refreshCache']) && $_POST['refreshCache']) {
                    $flickr->startClearCache();
                }
                /* figure out the album and/or photo to show */
                while ($i < count($parts)) {
                    if ((($parts[$i] == 'album') ||
                         ($parts[$i] == 'photo') ||
                         ($parts[$i] == 'gallerymenu') ||
                         ($parts[$i] == 'favorites'))
                        && !ereg(".html$", $parts[$i])) $request[$parts[$i]] = $parts[$i+1];
                    $i += 1;
                }
                if ($request['album']) {
                    $album = $flickr->getAlbum($request['album']);
                    $thumbpagenum = 1;
                    if(isset($_GET['page'])) {
                        $thumbpagenum = $_GET['page'];
                    }
                    
                    $Slickr_flickr_max_photos = get_option('Slickr_flickr_max_photos');
                    $photos = $this->getSlickrPhotos($request['album'], $Slickr_flickr_max_photos, $thumbpagenum);
                    $photos = $this->getSlickrPhotoSizes($photos);
                    $totalthumbpages = $photos['pages'];
                    /* This kludge spits out photo albums only, ie: no header, footer, widgets, etc! 
                    This winds up being called by AJAX and loaded into our thumbnail gallery */
                    header('Status: 200 OK'); /* Needed for the AJAX to work at all! */
                    header('HTTP/1.0 200 OK'); /* Needed for the AJAX to work at all! */

                    // Ania Krawet's fix for long descriptions with line breaks in them:
                    echo ("<p class=\"slickr_heading\">".html_entity_decode(str_replace("\n","<br>",$album['description']))."</p>");
                    if($totalthumbpages > 1) {
                        /* Spits out Page: 1 2 3 etc */
                        echo("\n<div id=\"slickr_pagelinks\"><p class=\"slickr_navigation\">page:");
                        for($i = 1; $i <= $totalthumbpages; $i++) {
                            if($i == $thumbpagenum) {
                                echo(" <a href=\"album/".$album['id']."/".$album['pagename']."?page=".$i."\" rel=\"slickrajaxcontent\" class=\"current\">".$i."</a>");
                            } else {
                                echo(" <a href=\"album/".$album['id']."/".$album['pagename']."?page=".$i."\" rel=\"slickrajaxcontent\" class=\"notcurrent\">".$i."</a>");
                            }
                        }
                        echo("</p></div>");
                    }
                    
                    /* Spits out our photo thumbnails: */
                    $thumbsize = get_option('Slickr_thumb_size');
                    foreach ($photos['photo'] as $photo) {
                        echo("\n<a href=\"".$photo['sizes']['Medium']['source']."\" rel=\"lightbox[".$album['id']."]\" title=\"".htmlspecialchars($photo['title'], ENT_QUOTES)."\"><img src=\"".$photo['sizes']['Square']['source']."\" alt=\"".htmlspecialchars($photo['title'], ENT_QUOTES)."\" width=\"".$thumbsize."\" height=\"".$thumbsize."\" /></a>");
                    }
                    
                    /* Navigation below thumbnails: */
                    if($totalthumbpages != 1) {
                        echo("<div id=\"slickr_navigation\">");
                        /* we have a previous page */
                        if($thumbpagenum > 1) {
                            echo("\n<p class=\"previous_album_photos\"><a href=\"album/".$album['id']."/".$album['pagename']."?page=".($thumbpagenum - 1)."\" rel=\"slickrajaxcontent\"> &laquo; previous</a></p>");
                        }
                        
                        /* We have more pages */
                        if($thumbpagenum < $totalthumbpages) {
                            echo("\n<p class=\"next_album_photos\"><a href=\"album/".$album['id']."/".$album['pagename']."?page=".($thumbpagenum + 1)."\" rel=\"slickrajaxcontent\">more photos &raquo;</a></p>");
                        }       
                        echo("</div>");                 
                    }
                    exit; /* Otherwise we get footer, etc. as well */
                
                } elseif ($request['gallerymenu']) {
                    header('Status: 200 OK'); /* Needed for the AJAX to work at all! */
                    header('HTTP/1.0 200 OK'); /* Needed for the AJAX to work at all! */
                    include(dirname(__FILE__).'/slickr/templates/galleries_menu.php');
                    exit; /* Otherwise we get footer, etc. as well */
                    
                } elseif ($request['favorites']) {
                    /* Not currently in use */
                    $album = $flickr->favorites_getList($album['owner'], $extras = NULL, $per_page = 50, $page = NULL);

                        /* This kludge spits out photo albums only, ie: no header, footer. widgets, etc! */
                        header('Status: 200 OK'); /* Needed for the AJAX to work at all! */
                        header('HTTP/1.0 200 OK'); /* Needed for the AJAX to work at all! */
                        echo ("<p class=\"gallery_name\">Some of my favorites</p>");
                        $thumbsize = get_option('Slickr_thumb_size');
                        foreach (($album['photo']) as $favphoto) {
                            echo("\n<a href=\"http://farm".($favphoto['farm']).".static.flickr.com/".($favphoto['server'])."/".($favphoto['id'])."_".($favphoto['secret']).".jpg\" rel=\"lightbox[favorites]\" title=\"".($favphoto['title'])."\"><img src=\"http://farm".($favphoto['farm']).".static.flickr.com/".($favphoto['server'])."/".($favphoto['id'])."_".($favphoto['secret'])."_s.jpg\" alt=\"".$photo['title']."\" width=\"".$thumbsize."\" height=\"".$thumbsize."\" /></a>");
                            
                        }                        
                        
                        exit; /* Otherwise we get footer, etc. as well */
                            
                } else {
                    $title = $photoAlbumTitle;
                    $albums = $flickr->getAlbums();
                    $favorites = $flickr->favorites_getList($album['owner'], $extras = NULL, $num_of_favs, $page = 1);
                    if($favorites != 0) { /* Do we have favorites on Flickr? */
                        /*print_r($albums);
                        print_r($favorites);*/
                    }
                    $photoTemplate = dirname(__FILE__)."/slickr/templates/albums-index.php";
                    wp_enqueue_script('slickr_js', "/wp-content/plugins/slickr-gallery/slickr/slickr.js", array('scriptaculous-effects'), '0.7');
                    add_action('wp_head', array(&$this, 'header'));
                }
                
                $errorMessages = $flickr->getErrorMsgs();
                if (is_object($Slickr)) {
                    if ($request['photo']) {
                        $Slickr->addBreadCrumb($photoAlbumTitle, '../../../../');
                        $Slickr->addBreadCrumb($album['title'], '../../'.$album['pagename']);
                        $Slickr->setPageTitle($photo['title']);
                    } elseif ($request['album']) {
                        $Slickr->addBreadCrumb($photoAlbumTitle, '../../');
                        $Slickr->setPageTitle($album['title']);
                    } else {
                        $Slickr->setPageTitle($photoAlbumTitle);
                    }
                }
            } else {
                $message = _("The photo album has not been configured.");
            }
            $mytheme = get_option('Slickr_themes_path')."/".urldecode(get_option('Slickr_theme'))."/view.php";
            include($mytheme);
            exit;
        }
    }
    function header() {
        include(dirname(__FILE__).'/slickr/templates/header.php');
    }
    
    // is the request coming in for photos
    function init() {
        $baseurl = get_option('Slickr_flickr_baseurl');
        if ($baseurl && (strpos($_SERVER['REQUEST_URI'], $baseurl) === 0)) {
            $_SERVER['_SLICKR_FLICKR_REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $_SERVER['REQUEST_URI'] = $baseurl;
            header('Status: 200 OK'); /* Needed for the AJAX to work at all! */
            header('HTTP/1.0 200 OK');
            //status_header(200); // ugly, just force a 200 status code
            add_action('template_redirect', array(&$this, 'template'));
        }
    }
    
    function addhooks() {
        add_options_page('Slickr Gallery', 'Slickr Gallery', 10, __FILE__, array(&$this, 'admin'));
        if (!ereg('^2.[1-9]', get_bloginfo('version')) && !ereg('^wordpress-mu-1.1', get_bloginfo('version'))) {
            add_filter('uploading_iframe_src', array(&$this, 'uploading_iframe'));
        }
    }
    
    function addPhotosTab() {
        add_filter('wp_upload_tabs', array(&$this, 'wp_upload_tabs'));
        add_action('upload_files_Slickr', array(&$this, 'upload_files_Slickr'));
    }
	
    function wp_upload_tabs ($array) {
        /*
         0 => tab display name, 
        1 => required cap, 
        2 => function that produces tab content, 
        3 => total number objects OR array(total, objects per page), 
        4 => add_query_args
	*/
	    $args = array();
        if ($_REQUEST['tags']) $args['tags'] = $_REQUEST['tags'];
        if ($_REQUEST['everyone']) $args['everyone'] = 1;
        $tab = array(
            'Slickr' => array('Photos', 'upload_files', array(&$this, 'photosTab'), array(100, 10), $args)
            );
        return array_merge($array, $tab);
    }
    // gets called before tabs are rendered
    function upload_files_Slickr() {
        //echo 'upload_files_Slickr';
    }
    function photosTab() {
        $perpage = 20;
        $tags = $_REQUEST['tags'];
        //$offsetpage = (int) ($_GET['start'] / $perpage) + 1;
        $offsetpage = (int) $_GET['paged'];
        $everyone = isset($_REQUEST['everyone']) && $_REQUEST['everyone'];
        //$usecache = ! (isset($_REQUEST['refresh']) && $_REQUEST['refresh']);

        $photos = $this->getRecentPhotos($tags, $offsetpage, $perpage, $everyone, $usecache);
        
        include(dirname(__FILE__).'/slickr/templates/admin-photos-tab.php');
    }
    	function media_buttons() {
	}
	function media_buttons_context($context) {
		global $post_ID, $temp_ID;
		$dir = dirname(__FILE__);

		$image_btn = get_option('siteurl').'/wp-content/plugins/slickr-gallery/slickr/templates/icon.gif';
		$image_title = 'Flickr Photos';
		
		$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);

		$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";
		$out = ' <a href="'.$media_upload_iframe_src.'&tab=slickr-photo-stream&TB_iframe=true&height=500&width=640" class="thickbox" title="'.$image_title.'"><img src="'.$image_btn.'" alt="'.$image_title.'" /></a>';
		return $context.$out;
	}
	
		// display photo stream
	function media_upload_content($mode='stream') {
        /*    
		if (!$this->options) $this->options = get_option('tantan_wordpress_s3');
		//if (!is_object($this->s3)) {
	        require_once(dirname(__FILE__).'/lib.s3.php');
	        $this->s3 = new TanTanS3($this->options['key'], $this->options['secret']);
	        $this->s3->setOptions($this->options);
        //}
        */
		add_filter('media_upload_tabs', array(&$this, 'media_upload_tabs'));
        add_action('admin_print_scripts', array(&$this, 'upload_tabs_scripts'));
		add_action('admin_print_scripts', 'media_admin_css');
		add_action('tantan_media_upload_header', 'media_upload_header');
		if ($mode == 'albums') {
			wp_iframe(array(&$this, 'albumsTab'));
		} elseif ($mode == 'everyone') {
		    $_REQUEST['everyone'] = true;
		    wp_iframe(array(&$this, 'photosTab'), 40);
		} else {
			wp_iframe(array(&$this, 'photosTab'), 40);
		}
	}
	function media_upload_tabs($tabs) {
		return array(
			'slickr-photo-stream' => __('Photo Stream'), // handler action suffix => tab text
			'slickr-photo-albums' => __('Albums'),
			//'slickr-photo-everyone' => __('Everyone'),
		);
	}
    
    // cleanup after yourself
    function deactivate() {
        require_once(dirname(__FILE__).'/slickr/lib.flickr.php');
        $flickr = new SlickrFlickr();
        if (is_writable(dirname(__FILE__).'/slickr/flickr-cache/')) {
            $flickr->clearCache();
        }
        if ($flickr->cache == 'db') {
            global $wpdb;
            $wpdb->query("DELETE FROM $flickr->cache_table;");
        }
    }

	/**
	* Adds in the necessary JavaScript files for the automated version
	**/
	function slickr_slider_js() {
		if (function_exists('wp_enqueue_script') && function_exists('wp_register_script')) {
			wp_enqueue_script('scriptaculous-slider');
		} else {
			slickr_slider_js_legacy();
		}
	}
	function slickr_slider_js_legacy() {
		if (function_exists('wp_enqueue_script') && function_exists('wp_register_script')) {  
            return; 
        }
        $admin_js_path =  get_bloginfo('wpurl')."/wp-includes/js/scriptaculous/";
        echo("
        <script type='text/javascript' src='".$admin_js_path."scriptaculous.js'></script>
        <script type='text/javascript' src='".$admin_js_path."prototype.js'></script>
        ");
	}

    function SlickrPlugin() {
        add_action('admin_menu', array(&$this, 'addhooks'));
        add_action('init', array(&$this, 'init'));
        add_action('deactivate_Slickr/flickr.php', array(&$this, 'deactivate'));
        add_action('load-upload.php', array(&$this, 'addPhotosTab'));
        add_action('admin_head', array(&$this, 'slickr_slider_js_legacy'));
        add_action('admin_print_scripts', array(&$this, 'slickr_slider_js'));
        // WP >= 2.5
		add_action('media_buttons', array(&$this, 'media_buttons')); 
		add_filter('media_buttons_context', array(&$this, 'media_buttons_context'));
		add_action('media_upload_slickr-photo-stream', array(&$this, 'media_upload_content'));
		add_action('media_upload_slickr-photo-albums', array(&$this, 'media_upload_content_albums'));
		//add_action('media_upload_slickr-photo-everyone', array(&$this, 'media_upload_content_everyone'));

    }

}
$SlickrPlugin =& new SlickrPlugin();
?>
