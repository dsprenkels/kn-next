<?php


// define constants
define('TEMPLATES_DIR', 'templates/');
define('SLIDESHOW_DIR', 'img/slideshow/');

// include TWIG
require_once("vendor/autoload.php");

// build new Twig Environment
$twig = new Twig_Environment(new Twig_Loader_Filesystem(TEMPLATES_DIR), array(
	'auto_reload' => true,
	'strict_variables' => true,
	'autoescape' => false,
));

// add email obscurify function
$email_function = new Twig_SimpleFunction('email', function ($addr) {
	return '<span class="email obfuscated">' . str_rot13($addr) . '</span>';
});
$twig->addFunction($email_function);

