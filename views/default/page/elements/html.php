<?php
/**
 * Page shell for all HTML pages (modified: see CHANGED)
 *
 * @uses $vars['html_attrs'] Attributes of the <html> tag
 * @uses $vars['head']       Parameters for the <head> element
 * @uses $vars['body_attrs'] Attributes of the <body> tag
 * @uses $vars['body']       The main content of the page
 */
// Set the content type
elgg_set_http_header("Content-type: text/html; charset=UTF-8");

$lang = get_current_language();

$default_html_attrs = [
	'xmlns' => 'http://www.w3.org/1999/xhtml',
	'xml:lang' => $lang,
	'lang' => $lang,
];
$html_attrs = elgg_extract('html_attrs', $vars, []);
$html_attrs = array_merge($default_html_attrs, $html_attrs);
$body_attrs = elgg_extract('body_attrs', $vars, []);

//-------------------- CHANGED from Elgg 3.0 ----------------------//

$head_content = elgg_extract('head', $vars, '');
$body_content = elgg_extract('body', $vars, '');

//get page title, description
preg_match('#<title>(.*?)</title#', $head_content, $title);
preg_match('#<meta name="description" content="(.*?)"#', $head_content, $description);

//check whether we are in fileview (workaround for Facebook not showing images)
preg_match('#/file/view/(\d+)#', $_SERVER['REQUEST_URI'], $fileview);

//add Open Graph protocol headers for page url, title & description
$head_content.="<meta property=\"og:url\" content=\"https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]\">\n";
$head_content.="<meta property=\"og:title\" content=\"$title[1]\">\n";
$head_content.="<meta property=\"og:description\" content=\"$description[1]\">\n";

//get image URLs except profile pic
preg_match_all('/<img.*?src="([^"]+)/',$body_content,$srcs);
if($fileview[1]){
	$srcs = Array("$_SERVER[HTTP_HOST]/serve-icon/$fileview[1]/large");
} else $srcs = preg_grep('/small\....$/', $srcs[1], PREG_GREP_INVERT);

//make og:image headers for images, except profile pic
foreach ($srcs as $ssl_src) {
	$src = "http://".$ssl_src;
	// $head_content.="<link rel=\"icon\" type=\"image/jpeg\" href=\"$src\">\n";
	$head_content.="<meta property=\"og:image\" content=\"$src\">\n";
}
?>
<!DOCTYPE html>
<html <?= elgg_format_attributes($html_attrs) ?>>
	<head>
		<?= $head_content ?>
	</head>
	<body <?= elgg_format_attributes($body_attrs) ?>>
		<?= $body_content ?>
	</body>
</html>
