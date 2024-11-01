<?php
/*
This is for the "K2" default Wordpress theme!
You may need to tweak this file a bit to get the Slickr album integrating nicely with the rest of your site.
Take a look at view-hemingway.php as an example of a view.php that integrates nicely with the Hemingway theme.

Take a look at your theme's index.php for guidance on tweaking this file.
*/

get_header();
?>

<div id="content">
<div class="post">
<div class="entry">

<?php
// load the appropriate albums index, album's photos, or individual photo template.
include($photoTemplate);
?>

</div>
</div>
</div>

  <div id="footer"></div>
</div><!--/left-col -->

<?php get_sidebar(); ?>
  
<?php get_footer(); ?>
