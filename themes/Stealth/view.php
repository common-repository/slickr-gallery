<?php 

/*
This blends view.php perfectly with the Stealth theme.
To create this I just looked at Stealth's index.php template. Do the same for other themes.
*/

get_header(); ?>



<table cellspacing="0" cellpadding="0">

<tr><td><img src="<?php bloginfo('stylesheet_directory'); ?>/images/rulu.gif" alt="rulu"></td><td class="z1"></td><td><img src="<?php bloginfo('stylesheet_directory'); ?>/images/ruru.gif" alt="ruru"></td></tr>

<tr class="z1"><td class="z1"></td><td>


<table cellspacing="0" cellpadding="0" width="425" class="n1">
<tr valign="top"><td class="grrlu"></td><td  class="grrru"></td></tr>
<td colspan="2" class="z2">

<?php
// load the albums index.
include($photoTemplate);
?>


</td></tr></table>

</td><td class="z1"></td></tr>
<tr><td><img src="<?php bloginfo('stylesheet_directory'); ?>/images/ruld.gif" alt="ruld"></td><td class="z1"></td><td><img src="<?php bloginfo('stylesheet_directory'); ?>/images/rurd.gif" alt="rurd"></td></tr>
</table>

<?php get_footer(); ?>
