<?php 

/*
This blends view.php perfectly with the Hemingway theme by Kyle Kneath.
To create this I just looked at Hemingway's index.php template. Do the same for other themes.
*/

get_header(); ?>


	<div id="primary">
		<div class="inside">
		<h3>Powered by <a href="http://www.stimuli.ca/slickr/" title="Slickr Gallery page">Slickr Gallery</a>.</h3>

<?php
// load the albums index.
include($photoTemplate);
?>

	   </div>
	</div>
	<!-- [END] #primary -->
	
<?php get_sidebar(); ?>
<?php get_footer(); ?>
