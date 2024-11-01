<?php
/*
This template displays one photo, along with links to the next and previous photo (if available)
*/
echo("
<div id=\"ancillary\">
<div class=\"inside\">
<h2>".$photo['title']."</h2>

<div id=\"photo\">
<img src=\"".$sizes['Medium']['source']."\" width=\"".$sizes['Medium']['width']."\" height=\"".$sizes['Medium']['height']."\"/>");

if (is_array($photo['notes']['note'])) {
    echo("\n<div id=\"notes\">");
    foreach (($photo['notes']['note']) as $note) {
        echo("<div class=\"note\" style=\"top:".$note['y']."px;left:".$note['x']."px;\">
        <div class=\"hover2\"><div class=\"hover\" style=\"width:".$note['w']."px;height:".$note['h']."px;\"></div></div>
            <div class=\"text\">".$note['_value']."</div>
        </div>");
    }
    echo("</div>");
}
echo("</div>

<p>".$photo['description']."</p>
<h4 id=\"comments\"><span class=\"num\">".count($comments)."</span> comments</h4>");

if (is_array($comments) && count($comments)) {

    foreach($comments as $comment) {
        echo("\n<em><a href=\"".$comment['author']['photosurl']."\">".$comment['author']['realname']."</a> wrote...</em><br>
        <p>".$comment['comment']."</p>");
    }
    echo("\n<p><a href=\"http://www.flickr.com/photos/".$photo['owner']['nsid']."/".$photo['id']."/#reply\">Add a comment &gt;</a></p>");

} else {

    echo("\n<p>No comments for this photo. 
    <a href=\"http://www.flickr.com/photos/".$photo['owner']['nsid']."/".$photo['id']."/#reply\">Add a comment &gt;</a></p>");

}

echo("\n<div id=\"context\">");
if ($context['prevphoto']['thumb']) {
    echo("\n<div class=\"prev\">
    <a href=\"../".$context['prevphoto']['id']."/".$context['prevphoto']['pagename']."\"><img src=\"".$context['prevphoto']['thumb']."\" /></a>
    <div class=\"label\">Previous Photo</div>
    </div>");

} else {

}

if ($context['nextphoto']['thumb']) {
    echo("\n<div class=\"next\">
    <a href=\"../".$context['nextphoto']['id']."/".$context['nextphoto']['pagename']."\"><img src=\"".$context['nextphoto']['thumb']."\" /></a>
    <div class=\"label\">Next Photo</div>
    </div>");

} else {

}

echo("\n</div>

<div class=\"flickr-meta-links\">
<a href=\"http://www.flickr.com/photos/".$photo['owner']['nsid']."/".$photo['id']."/\">View this photo on Flickr</a>
</div>
</div>
</div>");
?>