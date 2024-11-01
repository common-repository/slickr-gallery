<?php
global $SlickrPlugin;
$perpage = 10;

if (isset($_POST['refresh'])) $_GET['start'] = 0;

$offsetpage = (int) ($_GET['start'] / $perpage) + 1;

$tags = $_REQUEST['tags'];
$everyone = isset($_REQUEST['everyone']) && $_REQUEST['everyone'];
$usecache = ! (isset($_REQUEST['refresh']) && $_REQUEST['refresh']);

$extraVars = "&amp;everyone=$everyone&amp;usecache=$usecache&amp;tags=".urlencode($tags);

$photos = $SlickrPlugin->getRecentPhotos($tags, $offsetpage, $perpage, $everyone, $usecache);

$width = 0;
/* unneccessary: */
/*foreach ($photos as $photo) {
    $width += $photo['sizes']['Square']['width'];
}*/
$current_tab = ' class="current"';

$images_width = 75 + 35 + (count($photos) * 6);


$back = false;
$next = false;

if ($_GET['start']) {
    $back = (int) $_GET['start'] - $perpage;
}
$next = (int) $_GET['start'] + $perpage;

if (count($photos) < $perpage) { // no more!
    $next = false;
}

?>