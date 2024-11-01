<?php
/* Our Wordpress post photo insertion tab */
echo("<script type=\"text/javascript\">
var lastPhoto = false;
function Slickr_showOptions(id) {
    if (lastPhoto) Slickr_hideOptions(lastPhoto)
    lastPhoto = id

    var div = document.getElementById('options-'+id)
    if (div) div.style.display='block';
    return false;
}
function Slickr_hideOptions(id) {
    var div = document.getElementById('options-'+id)
    if (div) div.style.display='none';
    
    var e = window.event;
	if (e) {
        e.cancelBubble = true;
    	if (e.stopPropagation) e.stopPropagation();
    }
    return false;
}
function Slickr_addPhoto(photoUrl, sourceUrl, width, height, title) {
    var h = 
        '<a href=\"'+photoUrl+'\" title=\"'+title+'\">' +
        '<img src=\"'+sourceUrl+'\" alt=\"'+title+'\" width=\"'+width+'\" height=\"'+height+'\" class=\"slickr-post\" />' +
        '</a> '
        
    var win = window.opener ? window.opener : window.dialogArguments;
	if ( !win ) win = top;
	tinyMCE = win.tinyMCE;
	if ( typeof tinyMCE != 'undefined' && tinyMCE.getInstanceById('content') ) {
		tinyMCE.selectedInstance.getWin().focus();
		tinyMCE.execCommand('mceInsertContent', false, h);
	} else win.edInsertContent(win.edCanvas, h);

	return false;

/*			  
    if ( richedit ) {
        win.tinyMCE.execCommand('mceInsertContent', false, html);
    } else {
        win.edInsertContent(win.edCanvas, html);
    }
    return false;
    */
}
</script>

<style type=\"text/css\">
#upload-files a.file-link {
    width:75px;
    height:75px;
}
.photo-options {
    position:absolute;
    top:0px;
    left:0px;
    width:125px;
    padding:5px;
    
    background:white;
    opacity:0.9;
    border:1px solid #ccc;
    font-size:10px;
    z-index:10;
    
    display:none;
}
.photo-options div.close {
    position:absolute;
    top:2px;
    right:2px;
}
.alignleft {
    position:relative;
}
#upload-content {
    padding-top:10px;
}
</style>

<form method=\"get\" id=\"photos\" action=\"upload.php\">
<input type=\"hidden\" name=\"tab\" value=\"".$_GET['tab']."\" />
<input type=\"hidden\" name=\"post_id\" value=\"".$_GET['post_id']."\" />
<input type=\"hidden\" name=\"action\" value=\"".$_GET['action']."\" />
<input type=\"hidden\" name=\"style\" value=\"".$_GET['style']."\" />
<input type=\"hidden\" name=\"_wpnonce\" value=\"".$_GET['_wpnonce']."\" />
<input type=\"hidden\" name=\"ID\" value=\"".$_GET['ID']."\" />

Tags: <input type=\"text\" name=\"tags\" value=\"".$tags."\" size=\"30\" />
<input type=\"radio\" name=\"everyone\" value=\"\" ".(!$everyone ? 'checked=\"checked\"' : '')." id=\"showmine\" /><label for=\"showmine\"> My Photos </label> &nbsp;
<input type=\"radio\" name=\"everyone\" value=\"1\" ".($everyone ? 'checked=\"checked\"' : '')." id=\"showeveryone\" /><label for=\"showeveryone\"> Everyone </label>
&nbsp; &nbsp; 
<input type=\"submit\" name=\"refresh\" value=\"refresh\" />
<input type=\"button\" value=\"upload\" onclick=\"window.open('http://flickr.com/photos/upload/')\"/>
<!-- prolly should add a license field -->
</form>\n");
if (count($photos) == 0) {
    echo("Sorry, no photos found!");
} elseif (is_array($photos)) {
    /*$baseurl = get_option('Slickr_flickr_baseurl');
    $parts = parse_url(get_bloginfo('home'));
    $home = 'http://'.$parts['host'];*/
    
/* 
written by cameron@prolifique.com
Cameron wrote this so that multibyte languages don't get mangled, as they do with PHP's built in functions like htmlentities() 
*/

// Convert str to UTF-8 (if not already), then convert that to HTML named entities.
// and numbered references. Compare to native htmlentities() function.
// Unlike that function, this will skip any already existing entities in the string.
// mb_convert_encoding() doesn't encode ampersands, so use makeAmpersandEntities to convert those.
// mb_convert_encoding() won't usually convert to illegal numbered entities (128-159) unless
// there's a charset discrepancy, but just in case, correct them with correctIllegalEntities.
function makeSafeEntities($str, $convertTags = 0, $encoding = "") {
  if (is_array($arrOutput = $str)) {
    foreach (array_keys($arrOutput) as $key)
      $arrOutput[$key] = makeSafeEntities($arrOutput[$key],$encoding);
    return $arrOutput;
    }
  else if ($str !== "") {
    $str = makeUTF8($str,$encoding);
    $str = mb_convert_encoding($str,"HTML-ENTITIES","UTF-8");
    $str = makeAmpersandEntities($str);
    if ($convertTags)
      $str = makeTagEntities($str);
    $str = correctIllegalEntities($str);
    return $str;
    }
  }

// Convert str to UTF-8 (if not already), then convert to HTML numbered decimal entities.
// If selected, it first converts any illegal chars to safe named (and numbered) entities
// as in makeSafeEntities(). Unlike mb_convert_encoding(), mb_encode_numericentity() will
// NOT skip any already existing entities in the string, so use a regex to skip them.
function makeAllEntities($str, $useNamedEntities = 0, $encoding = "") {
  if (is_array($str)) {
    foreach ($str as $s)
      $arrOutput[] = makeAllEntities($s,$encoding);
    return $arrOutput;
    }
  else if ($str !== "") {
    $str = makeUTF8($str,$encoding);
    if ($useNamedEntities)
      $str = mb_convert_encoding($str,"HTML-ENTITIES","UTF-8");
    $str = makeTagEntities($str,$useNamedEntities);
    // Fix backslashes so they don't screw up following mb_ereg_replace
    // Single quotes are fixed by makeTagEntities() above
    $str = mb_ereg_replace('\\\\',"&#92;", $str);
    mb_regex_encoding("UTF-8");
    $str = mb_ereg_replace("(?>(&(?:[a-z]{0,4}\w{2,3};|#\d{2,5};)))|(\S+?)", "'\\1'.mb_encode_numericentity('\\2',array(0x0,0x2FFFF,0,0xFFFF),'UTF-8')", $str, "ime");
    $str = correctIllegalEntities($str);
    return $str;
    }
  }

// Convert common characters to named or numbered entities
function makeTagEntities($str, $useNamedEntities = 1) {
  // Note that we should use &apos; for the single quote, but IE doesn't like it
  $arrReplace = $useNamedEntities ? array('&#39;','&quot;','&lt;','&gt;') : array('&#39;','&#34;','&#60;','&#62;');
  return str_replace(array("'",'"','<','>'), $arrReplace, $str);
  }

// Convert ampersands to named or numbered entities.
// Use regex to skip any that might be part of existing entities.
function makeAmpersandEntities($str, $useNamedEntities = 1) {
  return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,5};)/m", $useNamedEntities ? "&amp;" : "&#38;", $str);
  }

// Convert illegal HTML numbered entities in the range 128 - 159 to legal couterparts
function correctIllegalEntities($str) {
  $chars = array(
    128 => '&#8364;',
    130 => '&#8218;',
    131 => '&#402;',
    132 => '&#8222;',
    133 => '&#8230;',
    134 => '&#8224;',
    135 => '&#8225;',
    136 => '&#710;',
    137 => '&#8240;',
    138 => '&#352;',
    139 => '&#8249;',
    140 => '&#338;',
    142 => '&#381;',
    145 => '&#8216;',
    146 => '&#8217;',
    147 => '&#8220;',
    148 => '&#8221;',
    149 => '&#8226;',
    150 => '&#8211;',
    151 => '&#8212;',
    152 => '&#732;',
    153 => '&#8482;',
    154 => '&#353;',
    155 => '&#8250;',
    156 => '&#339;',
    158 => '&#382;',
    159 => '&#376;');
  foreach (array_keys($chars) as $num)
    $str = str_replace("&#".$num.";", $chars[$num], $str);
  return $str;
  }

// Compare to native utf8_encode function, which will re-encode text that is already UTF-8
function makeUTF8($str,$encoding = "") {
  if ($str !== "") {
    if (empty($encoding) && isUTF8($str))
      $encoding = "UTF-8";
/*    if (empty($encoding))
      $encoding = mb_detect_encoding($str,'UTF-8, ISO-8859-1'); */
    if (empty($encoding))
      $encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
    return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str,"UTF-8",$encoding);
    }
  }

// Much simpler UTF-8-ness checker using a regular expression created by the W3C:
// Returns true if $string is valid UTF-8 and false otherwise.
// From http://w3.org/International/questions/qa-forms-utf-8.html
function isUTF8($str) {
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           // ASCII
       | [\xC2-\xDF][\x80-\xBF]            // non-overlong 2-byte
       | \xE0[\xA0-\xBF][\x80-\xBF]        // excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} // straight 3-byte
       | \xED[\x80-\x9F][\x80-\xBF]        // excluding surrogates
       | \xF0[\x90-\xBF][\x80-\xBF]{2}     // planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         // planes 4-15
       | \xF4[\x80-\x8F][\x80-\xBF]{2}     // plane 16
   )*$%xs', $str);
  }
/* 
End: written by cameron@prolifique.com
Cameron wrote this so that multibyte languages don't get mangled, as they do with PHP's built in functions like htmlentities() 
*/
    
    echo("<ul id='upload-files'>");
    
    foreach($photos as $photo) {
        $photoURL = $photo['sizes']['Medium']['source'];
        $big_photoURL = $photo['sizes']['Large']['source'];
    
        echo("\n<li id='flickr-photo-".$photo['id']."' class='alignleft'><a id='file-link-3' href='".$photoURL."' title='".makeSafeEntities($photo['title'])."' class='file-link image' onclick=\"return Slickr_showOptions(".$photo['id'].");\"><img id=\"image".$photo['id']."\" src=\"".$photo['sizes']['Square']['source']."\" alt=\"".makeSafeEntities($photo['title'])."\" height=\"".$photo['sizes']['Square']['height']."\" width=\"".$photo['sizes']['Square']['width']."\" style=\"border: 0px\"/></a>
    <div class=\"photo-options\" id=\"options-".$photo['id']."\">");
    
        foreach ($photo['sizes'] as $k => $size) {
                if (($k == "Medium") || ($k == "Large")) {
                        echo("<a href=\"".$size['source']."\" onclick=\"return Slickr_addPhoto('".$big_photoURL."', this, '".$size['width']."', '".$size['height']."', '".addslashes(makeSafeEntities($photo['title']))."');\">".$k."<span class=\"props\"> (".$size['width']."x".$size['height'].")</span></a><br />");
                } else {
                    echo("<a href=\"".$size['source']."\" onclick=\"return Slickr_addPhoto('".$photoURL."', this, '".$size['width']."', '".$size['height']."', '".addslashes(makeSafeEntities($photo['title']))."');\">".$k."<span class=\"props\"> (".$size['width']."x".$size['height'].")</span></a><br />");
                }
        }
    echo("<div class=\"close\"><a href=\"#\" onclick=\"return Slickr_hideOptions(".$photo['id'].");\">close</a></div>
    </div>
    </li>");
    }
    echo("\n</ul>");
}
?>