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
	return '<script type="text/javascript">document.write("' . str_rot13($addr) . '".rot13())</script>
<noscript>(e-mailadres verborgen)</noscript>'; 
});
$twig->addFunction($email_function);

$get_slideshow_images_function = new Twig_SimpleFunction('get_slideshow_images', function() {
	$files = scandir(SLIDESHOW_DIR);
	$result = array();
	foreach($files as $file) {
		if( is_file(SLIDESHOW_DIR . $file) ) {
			array_push($result, $file);
		}
	}
	return $result;
});
$twig->addFunction($get_slideshow_images_function);


$action = trim($_GET['action']);
switch ($action) {
	case 'over':
		$template = 'over.twig';
		break;
	case 'geschiedenis':
		$template = 'geschiedenis.twig';
		break;
	case 'activiteiten':
		$template = 'activiteiten.twig';
		break;
	case 'bestuur':
		$template = 'bestuur.twig';
		break;
	case 'aktanokturna':
		$template = 'aktanokturna.twig';
		break;
	case 'zusjes':
		$template = 'zusjes.twig';
		break;
	case 'sponsoren':
		$template = 'sponsoren.twig';
		break;
	case 'agenda':
		$template = 'agenda.twig';
		break;
	case 'lidworden':
		$template = 'lidworden.twig';
		break;
	case 'contact':
		$template = 'contact.twig';
		break;
	case 'index':
	default:
		$action = 'index';
		$template = 'index.twig';
		break;
}

$context = array(
	'action' => $action
);
$html = $twig->render($template, $context);

echo $html;
