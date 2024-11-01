<?php
/*
Plugin Name: Slickr Widget
Description: Adds a sidebar widget that shows your recent Flickr photos. Requires a <a href="http://www.stimuli.ca/lightbox" title="get Lightbox">Lightbox plugin</a> to work properly.
Author: Rupert Morris
Version: 1.0
Author URI: http://www.stimuli.ca/

Copyright (c) 2007

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

// Put functions into one big function we'll call at the plugins_loaded
// action. This ensures that all required plugin functions are defined.
function widget_Slickr_init() {

	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	// This is the function that outputs our little widget.
	function widget_Slickr($args) {
		
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);
        
        //Call our main Slickr class
        global $SlickrPlugin;

        if (is_object($SlickrPlugin)) {
            $randomize = get_option('Slickr_widget_randomize');
            $tags = ''; /* search ONLY for pics with these tags, leave blank to include all pics */
            $everyone = false; /* everyone's photos, or just yours? */
            $num = get_option('Slickr_widget_number_of_photos');/* how many photos matching above criteria to be fetched */
            $offset = '0';//get_option('Slickr_widget_offset'); /* recentness of first pic in our array; ie: 10 for tenth most recent, 0 for latest uploaded pic */
    
            if($randomize != 0) {
                $random_num = ($num * 3);
                $photos = $SlickrPlugin->getSlickrRecentPhotos($tags, $everyone, $random_num, $offset);
                shuffle($photos); /* randomize the photos for display */
                $photos = array_slice($photos, 0, $num); /* show 9 of the last $num_of_photos... of new photos */
            } else {
                $photos = $SlickrPlugin->getSlickrRecentPhotos($tags, $everyone, $num, $offset);
            }

    		// Each widget can store its own options. We keep strings here.
    		//$options = get_option('widget_Slickr');
    		//$title = $options['title'];
    		//$buttontext = $options['buttontext'];
    
    		// These lines generate our output. Widgets can be very complex
    		// but as you can see here, they can also be very, very simple.
    		echo $before_widget;
    		$url_parts = parse_url(get_bloginfo('home'));

            echo("\n".$before_title."Photos <a href=\"".get_option('Slickr_flickr_baseurl')."\">(view gallery)</a>". $after_title);
            echo("<div id=\"slickrwidget\" style=\"padding-top:5px;\">");
            foreach ($photos as $photo) {
                echo("<a href=\"".$photo['sizes']['Medium']['source']."\" rel=\"lightbox[hemingway_block]\" title=\"".htmlspecialchars($photo['title'], ENT_QUOTES)."\"><img style=\"padding:0px 3px 3px 0px;\" class=\"slickrwidget\" src=\"".$photo['sizes']['Square']['source']."\" width=\"".round($photo['sizes']['Square']['width']*0.75)."\" height=\"".round($photo['sizes']['Square']['height']*0.75)."\" alt=\"".htmlspecialchars($photo['title'], ENT_QUOTES)."\" /></a>");
            }
            
    		echo('</div>');
    		echo $after_widget."\n";
    	} else {
            echo("<strong>Error:</strong> Your Flickr photo album is not properly configured!</div>");
        }
    }

	function widget_Slickr_control() {
	
	   // Get our options and see if we're handling a form submission.
		$Slickr_widget_randomize = get_option('Slickr_widget_randomize');
		$Slickr_widget_number_of_photos = get_option('Slickr_widget_number_of_photos');
		
		if ( $_POST['Slickr-submit'] ) {
            $Slickr_widget_randomize = $_POST['Slickr_widget_randomize'];
            $Slickr_widget_number_of_photos = $_POST['Slickr_widget_number_of_photos'];
			// Remember to sanitize and format user input appropriately.
			update_option('Slickr_widget_number_of_photos', $Slickr_widget_number_of_photos);
			update_option('Slickr_widget_randomize', $Slickr_widget_randomize);
		}
		
		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		echo("Show <input type=\"text\" name=\"Slickr_widget_number_of_photos\" maxlength=\"2\" size=\"2\" value=\"".$Slickr_widget_number_of_photos."\" /> recent photos in the block. Randomize the photos:
<input type=\"checkbox\" name=\"Slickr_widget_randomize\" value=\"1\" ".($Slickr_widget_randomize ? 'checked="checked"' : '')." id=\"randomize\" />");
		echo '<input type="hidden" id="Slickr-submit" name="Slickr-submit" value="1" />';
	}
	
	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget(array('Slickr Photos', 'widgets'), 'widget_Slickr');

	// This registers our optional widget control form. Because of this
	// our widget will have a button that reveals a 300x100 pixel form.
	register_widget_control(array('Slickr Photos', 'widgets'), 'widget_Slickr_control', 300, 100);
}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_Slickr_init');

?>