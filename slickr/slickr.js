/* Slickr Gallery scripts ver. 1.0b
* (c) 2007 Rupert Morris 
* Feel free to use any or all of this script */
var slickrajaxcontent = Class.create();
var slickrchangeddiv;
var delayfades = '0.5';

slickrajaxcontent.prototype = {
	// Constructor runs on completion of the DOM loading. Loops through anchor tags looking for 
	// 'slickrajaxcontent' references and applies onclick events to appropriate links.
	initialize: function() {	
        if (!document.getElementsByTagName){ // safety catch in case of missing/invalid prototype.js
	       alert('Lightbox is not properly installed!');
	       alert('Grab a copy from http://www.stimuli.ca/lightbox/');
	       return; 
        }
		
        var anchors = document.getElementsByTagName('a');
		// loop through all anchor tags
		for (var i=0; i<anchors.length; i++){
			var anchor = anchors[i];
			var relAttribute = String(anchor.getAttribute('rel'));
			// use the string.match() method to catch 'slickrajaxcontent' references in the rel attribute
			if (anchor.getAttribute('href') && (relAttribute.toLowerCase().match('slickrajaxcontent|slickrajaxmenu'))){
                 anchor.onclick = function() { //When this matching item is clicked, trigger AJAX updates
                    slickrchangeddiv = this.getAttribute('rel'); //the area to update
                    slickrrelurl = this.getAttribute('href'); //the url that has the new content
                    //Element.show('slickrloading');//our spinner
                    Effect.Fade(slickrchangeddiv, { from: 1, to: 0.001, duration: delayfades } );
                    setTimeout("new Ajax.Updater(slickrchangeddiv, slickrrelurl, { delay: delayfades, method:'get' })",600);//pause the ajax so that fade finishes gracefully first
    				return false; // cancel the mouseclick
                }
            }
        }
    }
}

Ajax.Responders.register({
    onCreate: function(){
        //slickchangeddiv.innerHTML='<p class="slickr_heading">Requesting content...</p>';//<img src="loading.gif" alt="spinner" />';
    }, 
    onComplete: function(){
        //Element.hide('slickrloading');
        initLightbox();
        initslickrajaxcontent();
        Effect.Appear(slickrchangeddiv, { delay: delayfades, duration: 1.0, from: 0.001, to: 1.001 } );
    }
});

function initslickrajaxcontent() { 
    myslickrajaxcontent = new slickrajaxcontent(); 
}

function showslickrmenufirsttime() {
    Effect.Appear(document.getElementById("slickrajaxmenu"), { delay: delayfades, duration: 1.0, from: 0.001, to: 1.001 } );
    document.getElementById("slickrajaxcontent").innerHTML='<p class="slickr_heading">Select a Flickr album to view its photos.</p>';
}

Event.observe(window, 'load', initslickrajaxcontent, false);
Event.observe(window, 'load', showslickrmenufirsttime, false);