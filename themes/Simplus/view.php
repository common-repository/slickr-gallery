<?php get_header(); ?>

   <div class="content">

      <div class="post" id="post-<?php the_ID(); ?>">
         <div class="post2">
<?php
// load the albums index.
include($photoTemplate);
?>
</div>
</div>

<div class="nav"></div
<?php get_footer(); ?>
