<?php
/*
Copyright (C) 2007 Joe Tan, with modifications by Rupert Morris

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/
/* Our key/shared secret for Flickr.com: */
define("SLICKR_FLICKR_APIKEY", get_option('SLICKR_FLICKR_APIKEY'));
define("SLICKR_FLICKR_SHAREDSECRET", get_option('SLICKR_FLICKR_SHAREDSECRET'));

/* The db caching method is very experimental and hella slow! */
/* change "fs" to "db" to use database caching instead */
if (!defined("SLICKR_FLICKR_CACHEMODE")) 
define("SLICKR_FLICKR_CACHEMODE", "fs");

require_once(dirname(__FILE__)."/phpFlickr/phpFlickr.php");

class Slickr extends phpFlickr {
    var $_Slickr_apiKey;
    var $_Slickr_sharedSecret;
    var $_Slickr_user;
    var $_Slickr_useCache;
    var $_Slickr_errorCode;
    var $_Slickr_errorMsg;
    var $_Slickr_cacheExpire;
    var $_Slickr_options;
    
    function Slickr() {
        $this->_Slickr_apiKey = SLICKR_FLICKR_APIKEY;
        $this->_Slickr_sharedSecret = SLICKR_FLICKR_SHAREDSECRET;
        $this->_Slickr_errorCode = array();
        $this->_Slickr_errorMsg = array();
        $this->_Slickr_cacheExpire = -1; //3600;
        $this->_Slickr_options = array();
        
        parent::phpFlickr(SLICKR_FLICKR_APIKEY, SLICKR_FLICKR_SHAREDSECRET, false);
        if (SLICKR_FLICKR_CACHEMODE == 'db') {
            global $wpdb; // hmm, might need to think of a better way of doing this
            $this->enableCache('db', $wpdb);
        } else {
            $cacheDir = dirname(__FILE__).'/flickr-cache';
            if (!file_exists($cacheDir)) {
                mkdir($cacheDir, 0770);
            }
            $this->enableCache('fs', $cacheDir);
        }
    }
    
    function getAPIKey() {
        return $this->_Slickr_apiKey;
    }
    function getSharedSecret() {
        return $this->_Slickr_sharedSecret;
    }
    function getFrob() {
        $this->startClearCache();
        return $this->auth_getFrob();
    }
    
    function getUser() {
        return $this->_Slickr_user;
    }
    
    function setUser($user) {
        $this->_Slickr_user = $user;
    }
    
    function setOption($key, $value=NULL) {
        if (is_array($key)) {
            $this->_Slickr_options = $key;
        } else {
            $this->_Slickr_options[$key] = $value;
        }
    }
    function getOption($key) {
        return $this->_Slickr_options[$key];
    }
    
    function getRecent($extras = NULL, $per_page = NULL, $page = NULL) {
        $photos = $this->photos_getRecent($extras, $per_page, $page);
        $return = array();
        if (is_array($photos['photo'])) foreach ($photos['photo'] as $photo) {
            $row = array();
            $row['id'] = $photo['id'];
            $row['title'] = $photo['title'];
            $row['sizes'] = $this->getPhotoSizes($photo['id']);
            $row['pagename2'] = $this->_sanitizeTitle($photo['title']);
            $row['pagename'] = $row['pagename2'] . '.html';
            //$row['total'] = $photos['total'];
            $return[$photo['id']] = $row;
        }

        return $return;
    }
    
    function getPhotosByTags($tags) {
        $user = $this->auth_checkToken();
        //TODO: should disable caching here or something
        $photos = $this->search(array(
            'tags' => $tags,
            'user_id' => $user['user']['nsid'],
        ));
        return $photos;
    }
    function getRelatedTags($tags) { // hmm this gets everyones tags
        $tags = $this->tags_getRelated($tags);
        return $tags;
    }
    
    function getTags($count=100) {
        $data = $this->tags_getListUserPopular(NULL, $count);
        $return = array();
        if (is_array($data['tags']['tag'])) foreach ($data['tags']['tag'] as $tag) {
            $return[$tag['_value']] = $tag['count'];
        }
        return $return;
    }
    function getTagsByGroup($group_id, $count=100) {
        $return = array();
        return $return;
    }
    function getTagsByAlbum($album_id, $count=100) {
        $return = array();
        return $return;
    }
    
    // no caching
    function search($args) {
        $photos = $this->photos_search($args);
        $return = array();
        if (is_array($photos['photo'])) foreach ($photos['photo'] as $photo) {
            $row = array();
            $row['id'] = $photo['id'];
            $row['title'] = $photo['title'];
            $row['sizes'] = $this->getPhotoSizes($photo['id']);
            $row['pagename2'] = $this->_sanitizeTitle($photo['title']);
            $row['pagename'] = $row['pagename2'] . '.html';
            //$row['total'] = $photos['total'];
            $return[$photo['id']] = $row;
        }

        return $return;
    }
    
    function getAlbumsActual() { // get non cached list
        $this->startClearCache();
        $albums = $this->getAlbums();
        $this->doneClearCache();
        return $albums;
    }
    
    function getAlbums() {
        $albums = $this->photosets_getList();
        $return = array();
        if (is_array($albums['photoset'])) foreach ($albums['photoset'] as $album) {
            $row = array();
            $row['id'] = $album['id'];
            $row['title'] = $album['title'];
            $row['description'] = $album['description'];
            $row['primary'] = $album['primary'];
            $row['photos'] = $album['photos'];
            $row['pagename2'] = $this->_sanitizeTitle($album['title']);
            $row['pagename'] = $row['pagename2'] . '.html';
            $return[$row['id']] = $row;
        }
        return $return;
    }
    
    function getAlbum($album_id) {
        $album_id = $album_id . '';
        $album = $this->photosets_getInfo($album_id);
        $return = array();
        if (is_array($album)) {
            $return['id'] = $album['id'];
            $return['owner'] = $album['owner'];
            $return['primary'] = $album['primary'];
            $return['title'] = $album['title'];
            $return['description'] = $album['description'];
            $return['pagename2'] = $this->_sanitizeTitle($album['title']);
            $return['pagename'] = $return['pagename2'] . '.html';
            
        }
        return $return;
    }
    
    function getGroupsActual() {
        $this->startClearCache();
        $groups = $this->getGroups();
        $this->doneClearCache();
        return $groups;
    }
    function getGroups() {
        $groups = $this->groups_pools_getGroups();
        $return = array();
        if (is_array($groups)) foreach ($groups as $group) {
            $row = array();
            $row['id'] = $group['id'];
            $row['name'] = $group['name'];
            $row['photos'] = $group['photos'];
            $row['privacy'] = $group['privacy'];
            $row['admin'] = $group['admin'];
            $row['pagename2'] = $this->_sanitizeTitle($group['name']);
            $row['pagename'] = $row['pagename2'] . '.html';
            $row['iconurl'] = ($group['iconserver'] > 0) ? 'http://static.flickr.com/'.$group['iconserver'].'/buddyicons/'.$group['id'].'.jpg'
                : 'http://www.flickr.com/images/buddyicon.jpg';
            
            $info = $this->getGroup($group['id']);
            $row['description'] = $info['description'];
            $row['privacy'] = $info['privacy'];
            $row['members'] = $info['members'];
            $row['flickrURL'] = $info['flickrURL'];
            $return[$row['id']] = $row;
        }
        return $return;
    }
    function getGroup($group_id) {
        $group_id = $group_id . '';
        $group = $this->groups_getInfo($group_id);
        $return = array();
        if (is_array($group)) {
            $return['id'] = $group['id'];
            $return['name'] = $group['name'];
            $return['description'] = $group['description'];
            $return['members'] = $group['members'];
            $return['privacy'] = $group['privacy'];
            $return['pagename2'] = $this->_sanitizeTitle($group['name']);
            $return['pagename'] = $return['pagename2'] . '.html';
            $return['flickrURL'] = $this->urls_getGroup($group_id);
        }
        return $return;
    }
    
    function getPhotosByGroup($group_id, $tags=NULL) {
        $group_id = $group_id . '';
        
        $this->_Slickr_cacheExpire = 3600;
        $photos = $this->groups_pools_getPhotos($group_id, $tags);
        $this->_Slickr_cacheExpire = -1;
        
        
        $return = array();
        if (is_array($photos['photo'])) foreach ($photos['photo'] as $photo) {
            $row = array();
            $row['id'] = $photo['id'];
            $row['title'] = $photo['title'];
            $row['sizes'] = $this->getPhotoSizes($photo['id']);
            $row['pagename2'] = $this->_sanitizeTitle($photo['title']);
            $row['pagename'] = $row['pagename2'] . '.html';
            $return[$photo['id']] = $row;
        }
        return $return;
    }
        
    function getPhotos($album_id) {
        $album_id = $album_id . '';
        $photos = $this->photosets_getPhotos($album_id);
        if (is_array($photos) && $this->getOption('hidePrivatePhotos')) {
            foreach ($photos['photo'] as $k => $photo) {
                $perms = $this->photos_getPerms($photo['id']);
                if ($perms['ispublic'] != '1') {
                    unset($photos['photo'][$k]);
                }
            }
        }
        $return = array();
        if (is_array($photos)) foreach ($photos['photo'] as $photo) {
            $row = array();
            $row['id'] = $photo['id'];
            $row['title'] = $photo['title'];
            $row['sizes'] = $this->getPhotoSizes($photo['id']);
            $row['pagename2'] = $this->_sanitizeTitle($photo['title']);
            $row['pagename'] = $row['pagename2'] . '.html';
            $row = array_merge($row, $this->getPhoto($photo['id']));
            $return[$photo['id']] = $row;
        }

        return $return;
    }
    
    function getPhoto($photo_id) {
        $photo_id = $photo_id . '';
        $photo = $this->photos_getInfo($photo_id);
        return $photo;
    }
    function getComments($photo_id) {
        $photo_id = $photo_id . '';
        $this->_Slickr_cacheExpire = 3600;
        $comments = $this->photos_comments_getList($photo_id);
        $this->_Slickr_cacheExpire = -1;
        
        $return = array();
        if (is_array($comments)) foreach ($comments as $comment) {
            $row = array();
            $row['id'] = $comment['id'];
            $row['author'] = $this->people_getInfo($comment['author']);
            $row['datecreate'] = $comment['datecreate'];
            $row['permalink'] = $comment['permalink'];
            $row['comment'] = $comment['_value'];
            $return[$comment['id']] = $row;
        }
        return $return;
    }
    
    function getPhotoSizes($photo_id) {
        $photo_id = $photo_id . '';
        $sizes = $this->photos_getSizes($photo_id);
        $newsizes = array();
        foreach ($sizes as $k => $size) {
            unset($sizes[$k]['_name']);
            unset($sizes[$k]['_attributes']);
            unset($sizes[$k]['_value']);
            $newsizes[$sizes[$k]['label']] = $size;
        }
        return $newsizes;
    }
    
    function getContext($photo_id, $album_id='') {
        $photo_id = $photo_id . '';
        $album_id = $album_id . '';
        $context = array();
        if ($album_id) {
            $context = $this->photosets_getContext($photo_id, $album_id);
        } else {
            $context = $this->photos_getContext($photo_id);
        }
        $context['prevphoto']['pagename'] = $this->_sanitizeTitle($context['prevphoto']['title']).'.html';
        $context['nextphoto']['pagename'] = $this->_sanitizeTitle($context['nextphoto']['title']).'.html';
        return $context;
    }
    function getContextByGroup($photo_id, $group_id) {
        $photo_id = $photo_id . '';
        $group_id = $group_id . '';
        $context = $this->groups_pools_getContext($photo_id, $group_id);
        $context['prevphoto']['pagename'] = $this->_sanitizeTitle($context['prevphoto']['title']).'.html';
        $context['nextphoto']['pagename'] = $this->_sanitizeTitle($context['nextphoto']['title']).'.html';
        return $context;
    }

    function manualSort($array, $order) {
        if (!is_array($array)) { return array(); }
        
        if (is_array($order)) {
            $pre = array();
            //$mid = array();
            $pos = array();
            foreach ($order as $id => $ord) {
                if ($array[$id]) {
                    if (((int) $ord < 0)) { 
                        $pre[$id] = $array[$id];
                        unset($array[$id]);
                    } elseif (((int) $ord > 0)) { 
                        $pos[$id] = $array[$id];
                        unset($array[$id]);
                    //} else {
                        //$mid[$id] = $array[$id];
                    }
                }
            }
            /*foreach ($array as $id => $elem) {
                $new[$id] = $array[$id];
            }*/
            return $pre + $array + $pos;
            //return array_merge($pre, array_merge($array, $pos));
        } else {
            return $array;
        }
    }

    function startClearCache() {
        $this->_Slickr_useCache = false;
    }
    function doneClearCache() {
        $this->_Slickr_useCache = true;
    }
    function clearCache() {
        if ($this->_clearCache($this->cache_dir)) {
            return @mkdir($this->cache_dir, 0770);
        } else {
            return false;
        }
    }
    function _clearCache($dir) {
       if (substr($dir, strlen($dir)-1, 1) != '/')
           $dir .= '/';
    
    
       if ($handle = opendir($dir)) {
           while ($obj = readdir($handle)) {
               if ($obj != '.' && $obj != '..') {
                   if (is_dir($dir.$obj)) {
                       if (!$this->_clearCache($dir.$obj))
                           return false;
                   }
                   elseif (is_file($dir.$obj)) {
                       if (!unlink($dir.$obj)) return false;
                   }
               }
           }
           closedir($handle);
    
           if (!@rmdir($dir)) return false;
           return true;
       }
       return false;
    }
    function _sanitizeTitle($title) {
        // try this WP function sanitize_title_with_dashes()
        
       // Replace spaces with underscores
       $output = preg_replace("/\s/e" , '_' , $title);
       // Remove non-word characters
       $output = preg_replace("/\W/e" , "" , $output);
       return $output;
    }
    function getErrorMsgs() {
        return implode("\n", $this->_Slickr_errorMsg);
    }
        
    /*
        Reimplemented methods
    */
    
    function request ($command, $args = array(), $nocache = false) {
        $nocache = (($this->_Slickr_cacheExpire > 0) ? true : false);
        $nocache = ($nocache ? true : 
            ($this->_Slickr_useCache ? false : true));
        parent::request($command, $args, $nocache);
    }

    
    function enableCache($type, $connection, $cache_expire = 600, $table = 'flickr_cache') {
        
        if ($type == 'db') {
            $this->cache = 'db';
            $this->cache_db =& $connection;
            $this->cache_table = 'Slickr_flickr_cache';
            
        } elseif ($type == 'fs') {
            $this->cache = 'fs';
            $this->cache_expire = $cache_expire;
            $this->cache_dir = $connection;
            $this->_Slickr_useCache = true;
        }
    }

    function getCached ($request) // buggy, time based caching doesnt work
    {
        if ($this->cache == 'db') {
            $result = $this->cache_db->get_col("SELECT response FROM " . $this->cache_table . " WHERE request = '" . $reqhash . "'");
            if (!empty($result)) {
                return $result;
            }
            return false;
        } elseif ($this->cache == 'fs') {
            //Checks the database or filesystem for a cached result to the request.
            //If there is no cache result, it returns a value of false. If it finds one,
            //it returns the unparsed XML.
            $reqhash = md5(serialize($request));
            $pre = substr($reqhash, 0, 2);
            $file = $this->cache_dir . '/' . $pre . '/' . $reqhash . '.cache';
    
            if (file_exists($file)) {
                if ($this->_Slickr_cacheExpire > 0) {
                    if ((time() - filemtime($file)) > $this->_Slickr_cacheExpire) {
                        return false;
                    }
                } 
                return file_get_contents($file);
            } else {
                return false;
            }
        }
    }
    
    function cache ($request, $response) 
    {
        $reqhash = md5(serialize($request));
        if ($this->cache == 'db') {
            $this->cache_db->query("DELETE FROM $this->cache_table WHERE request = '$reqhash'");
            $sql = "INSERT INTO " . $this->cache_table . " (request, response, expiration) VALUES ('$reqhash', '" . addslashes($response) . "', '" . strftime("%Y-%m-%d %H:%M:%S") . "')";
            $this->cache_db->query($sql);
        } elseif ($this->cache == 'fs') {
            //Caches the unparsed XML of a request.
            
            $pre = substr($reqhash, 0, 2);  // store into buckets
            $file = $this->cache_dir . "/" . $pre . '/' . $reqhash . ".cache";
            
            if (!file_exists($this->cache_dir . '/' . $pre)) {
                mkdir($this->cache_dir . '/' . $pre, 0770);
            }
            $fstream = fopen($file, "w");
            $result = fwrite($fstream,$response);
            fclose($fstream);
            return $result;
        }
    }
    function auth_getToken ($frob) 
    {
        /* http://www.flickr.com/services/api/flickr.auth.getToken.html */
        $this->request('flickr.auth.getToken', array('frob'=>$frob));
        //session_register('phpFlickr_auth_token');
        $_SESSION['phpFlickr_auth_token'] = $this->parsed_response['auth']['token'];
        return $this->parsed_response ? $this->parsed_response['auth']['token'] : false;
    }
}
?>