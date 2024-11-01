<?php 
/*
This blends view.php perfectly with the Fresh 2.0 theme by Rupert Morris.
To create this I just looked at Fresh's index.php template. Do the same for other themes.
*/

get_header(); ?>

<?php get_sidebar(); ?>

	<div id="page">
	   <div class="post">
	   
<?php
// load the albums index.
include($photoTemplate);
?>

        </div>
<?php get_footer(); ?>
