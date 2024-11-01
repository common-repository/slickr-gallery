<?php
/*
This is for the "K2" default Wordpress theme!
You may need to tweak this file a bit to get the Slickr album integrating nicely with the rest of your site.
Take a look at view-hemingway.php as an example of a view.php that integrates nicely with the Hemingway theme.

Take a look at your theme's index.php for guidance on tweaking this file.
*/

get_header();
?>

<div id="content" class="narrowcolumn">
<div class="post">

<?php
// load the appropriate albums index, album's photos, or individual photo template.
include($photoTemplate);
?>

</div>
</div>

<?php
get_sidebar();
get_footer();
?>
