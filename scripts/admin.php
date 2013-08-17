<?php

/*
 * There exists only one file for admin-related business, for simplicity.
 * This file may include multiple pages.
 */

// define constants
define('TEMPLATES_DIR', 'templates/');
define('SLIDESHOW_DIR', 'slideshow/');

function get_slideshow_images() {
	$files = scandir(SLIDESHOW_DIR);
	$result = array();
	foreach($files as $file) {
	    if( is_file(SLIDESHOW_DIR . $file) ) {
			array_push($result, $file);
		}
	}
	return $result;
}

function slideshow_delete() {
	$files = get_slideshow_images();
	print_r($_POST);
	foreach ($_POST as $checkbox => $filename) {
		assert(in_array($filename, $files));
		assert(strpos($filename, '/') === FALSE);
	}
	foreach ($_POST as $checkbox => $filename) {
		unlink(SLIDESHOW_DIR . $filename);			
	}
}

function slideshow_add() {
	assert(array_key_exists("new_slideshow_image", $_FILES));
	if ($_FILES["new_slideshow_image"]["error"] > 0) {
		die("Error: " . $_FILES["new_slideshow_image"]["error"] . "<br>");
	}
	$filename = trim($_FILES["new_slideshow_image"]["name"]);
	assert(strpos($filename, '/') === FALSE);
	$new_path = SLIDESHOW_DIR . $filename;
	copy($_FILES["new_slideshow_image"]["tmp_name"], $new_path);
}

$REQUEST_HEADERS = getallheaders();

// authenticate the user, if Authorization is used
session_start();
if (isset($REQUEST_HEADERS['Authorization'])) {
	$auth_header = explode(' ', $REQUEST_HEADERS['Authorization']);
	$credentials = explode(':', base64_decode($auth_header[1]), 2);
	if ($credentials[0] === "marco" && $credentials[1] === "polo") {
		$_SESSION['user'] = "marco";
	}
}

// user is not authenticated, deny the page
if (! isset($_SESSION['user'])) {
	header('WWW-Authenticate: Basic realm="Karpe Noktem site Admin"');
	http_response_code(401);
	exit();
}

// log out
if (isset($_GET['logout'])) {
	session_destroy();
	header('Location: .');
	http_response_code(303);
	exit();
}

// handle post request
if (! empty($_POST)) {
	$action = $_GET['action'];
	switch ($action) {
		case 'slideshow_add':
			slideshow_add();
			break;
		case 'slideshow_delete':
			slideshow_delete();
			break;
	}
}

// slideshow change

// bigpicture change

// call make





// include TWIG
require_once("../vendor/autoload.php");

// build new Twig Environment
$twig = new Twig_Environment(new Twig_Loader_Filesystem(TEMPLATES_DIR), array(
	'auto_reload' => true,
	'strict_variables' => true,
	'autoescape' => false,
));

// add a rot13 filter for email
$rot13_filter = new Twig_SimpleFilter('rot13', 'str_rot13');
$twig->addFilter($rot13_filter);

// add email obscurify function
$email_function = new Twig_SimpleFunction('email', function ($addr) {
	return '<script type="text/javascript">document.write("' . str_rot13($addr) . '".rot13())</script>
<noscript>(e-mailadres verborgen)</noscript>'; 
});
$twig->addFunction($email_function);


$context = array(
	'php_self' => 'admin.php',
	'slideshow_images' => get_slideshow_images()
);


$html = $twig->render('admin.twig', $context);

echo $html;

?>