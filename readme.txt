=== Slickr Gallery ===
Contributors: Rupert Morris
Tags: AJAX, image, lightbox, gallery, flickr, photo, lightbox
Requires at least: 2.0.1
Tested up to: 2.3.1
Stable tag: 0.6.5

== Installation ==

To do a new installation of the plugin, please follow these steps

1. Download the zipped plugin file to your local machine.
2. Unzip the file.
3. Upload the `slickr-gallery` folder to the `/wp-content/plugins/` directory.
4. Activate the plugin through the 'Plugins' menu in WordPress.
5. Go to Options -> Slickr Gallery and set it up how you want it.
6. Make sure you have a lightbox plugin enabled as well. Grab one at http://stimuli.ca/lightbox/
7. You need to change your post options from the default ?=172 to /year/month/day/ or /postname/.

The folders in the "themes" directory contain view.php and slickr.css files. If one of those folders is the same name as your Wordpress theme, you are in luck... select that theme from the Options -> Slickr Gallery page.

If your theme doesn't have a corresponding folder in the "slickr/flickr/themes" directory, you can either:

1. Try your luck with the default theme. Failing that, try some others ;)
2. *Duplicate* the Default folder, renaming it to Custom or MyTheme or similar. Failure to *duplicate and rename* the folder will result in it being over-written should you upgrade Slickr Gallery in the future. Edit the CSS, and if necessary, the view.php file, to make your own theme. Then select it from the Options -> Slickr Gallery page. BACK IT UP on your harddrive so you can add it again for future versions of Slickr Gallery.

If you use a popular wordpress theme, feel free to send your edited Slickr Gallery theme to me and I will consider it for inclusion with Slickr Gallery.

If you are a Hemingway user, and want your Slickr Gallery Hemingway block to look like mine, take a look at the bottom of my Hemingway style sheet, and copy/paste the code into your own:
http://www.stimuli.ca/wordpress/wp-content/themes/hemingway-ajax/style.css

Enjoy!

Rupert Morris
rustyvespa@gmail.com
www.stimuli.ca/slickr/

== Frequently Asked Questions ==

Q: Why doesn't it work for me?

A: Either:

1. You renamed Slickr Gallery's root folder to something other than `slickr-gallery`. 
2. You have other plugins that conflict with slickr, such as Weather Icon or ShareThis. Disable your other plugins and see if that helps. If it does, re-enable each plugin, one at a time to see which one is causing the conflict.
3. Try my version of the lightbox javascripts. This is what I use. http://www.stimuli.ca/lightbox/
4. Your webserver doesn't support the necessary basic technologies to run the plugin. If this is the case you are out of luck.

