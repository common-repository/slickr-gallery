<?php
/* includes our CSS and javascripts for Slickr Gallery pages to run */
echo("
<!-- begin slickr -->
<link rel=\"stylesheet\" href=\"".get_settings('siteurl')."/wp-content/plugins/slickr-gallery/themes/".urldecode(get_option('Slickr_theme'))."/slickr.css\" media=\"all\" />

<script type=\"text/javascript\">
/* for Flickr flash slideshows */
function openSlideShow(id) {
    var url = \"http://flickr.com/slideShow/index.gne?set_id=\"+id
    var w = window.open(url, '_blank', 'width=500,height=500');
    if (w) w.focus()
    return false;
}
</script>

<script type=\"text/javascript\" src=\"".get_settings('siteurl')."/wp-content/plugins/slickr-gallery/slickr/slickr.js\"></script>
<!-- end slickr -->");
?>