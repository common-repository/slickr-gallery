<?php
/*
This is for the "MistyLook" Wordpress theme!
You may need to tweak this file a bit to get the Slickr album integrating nicely with the rest of your site.
Take a look at your theme's page.php for guidance on tweaking this file.
*/

get_header(); 
?>

<div id="content">
    <div id="content-main">
    	<div class="post">

<?php
// load the appropriate albums index, album's photos, or individual photo template.
include($photoTemplate);
?>
        </div>
    </div>
<?php get_sidebar();
get_footer();
?>
