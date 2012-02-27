<?php
/**
You shouldn't edit this file unless you know what you are doing.  
This file won't update the content, only the presentation of the
content.  Edit the page that calls this file instead.  
*/

function head($local_doctype=NULL, $local_title=NULL, $local_description=NULL, $local_keywords=NULL, $local_header=NULL, $local_js=NULL, $local_refresh=NULL) { 
    global $DOCTYPE, $TITLE, $DESCRIPTION, $KEYWORDS, $HEADER, $CDN, $JS, $REFRESH;

    if (isset($local_doctype))
        $DOCTYPE = $local_doctype;

    if (isset($local_title))
        $TITLE = $local_title;

    if (isset($local_description))
        $DESCRIPTION = $local_description;
    else if (!isset($DESCRIPTION) && isset($TITLE))
        $DESCRIPTION = $TITLE;

    if (isset($local_keywords))
        $KEYWORDS = $local_keywords;

    if (isset($local_header))
        $HEADER = $local_header;

    if (isset($local_js))
        $JS = $local_js;

    if (isset($local_refresh))
        $REFRESH = $local_refresh;

    if (strcmp($DOCTYPE, 'xhtml1-transitional') == 0)
        $doctype_html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    else if (strcmp($DOCTYPE, 'xhtml11') == 0)
        $doctype_html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
    else if (strcmp($DOCTYPE, 'xhtml1-strict') == 0)
        $doctype_html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    else
        $doctype_html = NULL;

    if (!isset($doctype_html) || !isset($TITLE) || !isset($DESCRIPTION) || !isset($KEYWORDS) || !isset($HEADER) || !isset($CDN)) {
        echo 'Go fix the config file, some attribute(s) may not be set...'; 
        exit;
    }

    $script_javascript = NULL;
    if (!empty($JS))
        $script_javascript = "<script type=\"text/javascript\" src=\"$JS\"></script>";

    $meta_refresh = NULL;
    if (!empty($REFRESH))
        $meta_refresh = "<meta http-equiv=\"Refresh\" content=\"$REFRESH\" />";

?>
<?=$doctype_html?>
<!-- Open Systems Solutions -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title><?=$TITLE?></title>
                <meta name="language" content="en" />
                <meta name="author" content="Rutgers University" />
		<meta name="description" content="<?=$DESCRIPTION?>" />
		<meta name="keywords" content="<?=$KEYWORDS?>" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <?=$meta_refresh?>
		<link rel="stylesheet" type="text/css" href="<?=$CDN?>/style.css" media="screen" />
		<link rel="icon" type="image/vnd.microsoft.icon" href="<?=$CDN?>/images/favicon.ico" />
                <script type="text/javascript" src="<?=$CDN?>/js/generic.js"></script>
                <?=$script_javascript?>
	</head>
	<body onload="loadNews('cssnews-loader');loadServiceNews('servicenews-loader');">
	   	<div id="header">
	        <div id="seal"></div>
	        <div id="logo"><a href="http://www.rutgers.edu"><img alt="logo" src="<?=$CDN?>/images/logo.png" /></a></div>
	        <div id="listmenu">
	            <ul>
	                <li><a href="http://webmail.rutgers.edu/">Webmail</a></li>

	                <li><a href="https://email.rutgers.edu/mailman/">Mailman</a></li>
	                <li><a href="https://rams.rutgers.edu/">RAMS</a></li>
	                <li><a href="#">More...</a>
	                <!-- note the proper way to make nested menus w/xhtml...nested list inside of <li> </li> -->
	                    <ul>
	                        <li><a href="https://rats.rutgers.edu">RATS</a></li>
	                        <li><a href="https://rim.rutgers.edu/jwchat/">RIM</a></li>

	                        <li><a href="http://rpm.rutgers.edu/">RPM</a></li>
	                    </ul>
	                </li>
	            </ul>
	        </div>
            <div id="search">
                <form method="get" action="http://www.google.com/u/rutgerz" id="gs">
 					<fieldset>

                    <input type="hidden" name="hl" value="en" />
                    <input type="hidden" name="ie" value="ISO-8859-1" />
                    <label for="q">Search: </label><input type="text" id="q" name="q" size="10" maxlength="2048" value="" />
                    <input id="go_button" type="image" src="<?=$CDN?>/images/gobutton.gif" alt="Submit button" />
					</fieldset>
				</form>
            </div>

	        <div id="link_line">
	            <a href="http://css.rutgers.edu/">CSS Home</a> | <a href="http://www.rutgers.edu/">Rutgers Home</a> | <a href="http://search.rutgers.edu/">Search</a>
	        </div>
	        <div id="menu"><?=$HEADER?></div>
	    </div>
                <div id="main">
<?php
}

function right_boxes($top=NULL, $bottom=NULL) {
    global $LOGIN_BOX_HEADER, $LOGIN_BOX_SUB_HEADER, $LOGIN_BOX_FOOTER,
            $RSS_SERVICE_URL, $RSS_SERVICE_ITEM_MAX;

    $right_top = NULL;
    $right_middle = NULL;
    $right_bottom = NULL;
    $news = false;
    $login_box = false;
    $service_news = false;

    if (!isset($top) && !isset($bottom))
        return;

    if ((isset($top) && is_string($top) && strcmp($top, 'news') == 0) 
        || (isset($bottom) && is_string($bottom) && strcmp($bottom, 'news') == 0))
        $news = true;

    if (isset($top) && is_array($top)) {
        if (isset($top['form']) && is_readable($top['form'])) {
            $login_box = true;
            $login_form_file = $top['form'];
        }
        if (isset($top['rss']) && $top['rss'])
            $service_news = true;
    } else if (isset($bottom) && is_array($bottom)) {
        if (isset($bottom['form']) && is_readable($bottom['form']))
            $login_box = true;
            $login_form_file = $bottom['form'];
        if (isset($bottom['rss']) && $bottom['rss'])
            $service_news = true;
    }

    // Do we need a RSS news feed for CSS?
    if ($news)
        $right_top = "<div id='cssnews-loader'>CSS News loading:<img src='/css/newimages/rss-loader.gif' alt='RSS loader image' /></div>";

    // Do we need a login box?
    if ($login_box) {
        // Try reading the form file
        $form_html = NULL;
        $form_html = file_get_contents($login_form_file);
        $right_middle = <<<HTML
            <div id="right_middle">                          
            <div id="login-outer">                  
            <div id="login-box">
            <div id="login-top-center">$LOGIN_BOX_HEADER</div>
            <div id="login-poweredby">$LOGIN_BOX_SUB_HEADER</div>
            <div id="login-fields">
            $form_html
            </div>
            $LOGIN_BOX_FOOTER
            </div>                                                                  
            </div>                                  
            </div>                          
HTML;
    }
 
    // Do we need a RSS news feed for the service? (HTML accordingly if yay or nay)
    if ($service_news) {
            $right_bottom =<<<HTML
            <input type='hidden' id='rss_service_url' value='$RSS_SERVICE_URL' />
            <input type='hidden' id='rss_service_item_max' value='$RSS_SERVICE_ITEM_MAX' />
            <div id='servicenews-loader'>Service News loading:
            <img src='/css/newimages/rss-loader.gif' alt='RSS loader image' />
            </div>
HTML;
    }
      
    // Determine what type of service news we need
    if (!$login_box && $service_news)
            $right_bottom .= "<input type='hidden' id='rss_type' value='news' />";
    else if ($login_box && $service_news)
            $right_bottom .= "<input type='hidden' id='rss_type' value='box-and-news' />";
    else if ($login_box && !$service_news) {
        $right_bottom =<<<HTML
        <div id="right_bottom">
        <table id="login-bottom">
        <tfoot>
        <tr><td class="login-bottom-footer-empty" colspan="1"></td></tr>
        </tfoot>
        <tbody id="rss-service-body-item">
        <tr><td class="empty"></td></tr>
        </tbody>
        </table>
        </div>
HTML;
    }
?>
                        <div id="right_boxes">
				<?=$right_top?>
				<?=$right_middle?>
				<?=$right_bottom?>
			</div>

<?php
}

function content() {
    global $DIV_ID_CONTENT_CLASS;
?>
                        <div id="content" class="<?=$DIV_ID_CONTENT_CLASS?>">
                                <div id="left">

<?php
}

function foot($local_contact=NULL, $validxhtml=true) { 
    global $CONTACT, $DOCTYPE;
    if (isset($local_contact))
        $CONTACT = $local_contact;

    $validtext = NULL;
    if ($validxhtml) {
        if (isset($DOCTYPE) && strcmp($DOCTYPE, 'xhtml11') == 0)
            $validtext = ' - This site is <a href="http://validator.w3.org/check?uri=referer">XHTML 1.1 valid</a>';
        else
            $validtext = ' - This site is <a href="http://validator.w3.org/check?uri=referer">XHTML 1.0 valid</a>';
    }

?>
                                </div>
                        </div>
                </div>
		<div id="footer">
			&copy; <?echo date('Y')?> <a href="http://www.rutgers.edu">Rutgers, The State University of New Jersey</a> - Contact us (<a href="mailto:<?=$CONTACT?>"><?=$CONTACT?></a> or 732-445-HELP)<?=$validtext?>

		</div>
	</body>
</html>

<?php 
}

function getRSS($rssurl) {
    require_once "XML/RSS.php";

    global $RSS_TIMEOUT;

    $merged_arr = array();

    // cURL remote RSS URL
    $ch = curl_init($rssurl);

    // return the transfer as a string and set timeouts
    // see http://us2.php.net/manual/en/function.curl-setopt.php
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $RSS_TIMEOUT);
    curl_setopt($ch, CURLOPT_TIMEOUT, $RSS_TIMEOUT+2);

    // exec the session
    $contents = curl_exec($ch);

    // check if any error occured
    if(curl_errno($ch)) {
        // close the session and return
        curl_close($ch);
        
        return false;
    }

    // close the session
    curl_close($ch);

    // parse RSS feed into homebrewed array
    $rss = new XML_RSS($contents);
    $rss->parse();

    $channel = $rss->getChannelInfo();
    if (isset($channel))
        $merged_arr['channel'] = $channel;

    $items = $rss->getItems();
    if (isset($items))
        $merged_arr['items'] = $items;

    return $merged_arr;

}
?>
