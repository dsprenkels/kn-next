<?php

require_once('common.php');

// define constants
define('AGENDA_JSON_FILE', '/tmp/kn-next_agenda.json');
define('FETCH_AGENDA_SCRIPT', __DIR__ . '/utils/fetch_agenda.py');


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

// create an empty $context, so extra values can be defined
$context = array();

// do page-specific tasks
$action = isset($_GET['action']) ? trim($_GET['action']) : '';
switch (strtolower($action)) {
	case 'lustrum':
	case 'lustrumposter':
	case 'intro2008':
	case 'intro2009':
	case 'intro2010':
	case 'introposter2009':
	case 'introposter2010':
	case 'introposter2011':
	case 'introposter2012':
	case 'introposter2013':
	case 'links':
	case 'media':
	case 'merchandise':
	case 'openweekposter2013':
	case 'release':
	case 'default': // alias ...
	case 'home':    // alias ...
		header('Status: 301 Moved Permanently', true, 301);
		header('Location: /');
		exit;
	case 'watis': // alias for historical reasons, do not use
		header('Status: 301 Moved Permanently', true, 301);
		header('Location: /over');
		exit;
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
		$action = 'bestuur10';
		$template = 'bestuur/bestuur10.twig';
		break;
	case 'aktanokturna':
	case 'akta': // alias ...
	case 'an':   // alias ...
		$template = 'aktanokturna.twig';
		$context['aktas'] = array();
		foreach(glob('aktas/Jaargang*') as $jaargang) {
			preg_match('/Jaargang ([0-9]+)/', $jaargang, $m);
			$context['aktas'][(int)$m[1]] = array(
				'name' => 'Jaargang '. $m[1],
				'aktas' => array(),
			);
			foreach(glob($jaargang .'/*\.pdf') as $akta) {
				preg_match('|/([^/]*)\.pdf|', $akta, $m2);
				$context['aktas'][$m[1]]['aktas'][] = array(
					'title' => $m2[1],
					'location' => $akta,
				);
			}
			if(count($context['aktas'][$m[1]]['aktas']) == 0) {
				unset($context['aktas'][$m[1]]);
			}
		}
		ksort($context['aktas']);
		$context['aktas'] = array_reverse($context['aktas']);
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
	case 'route':
		$template = 'route.twig';
		break;
	case 'index':
	case 'index.php': //alias ...
		$action = 'index';
		$template = 'index.twig';
		break;
	case 'baragenda': // redirect
		header('Status: 301 Moved Permanently', true, 301);
		header('Location: /planning');
		exit;
	case 'robots.txt':
		header('Content-type: text/plain');
		$template = 'robots.txt';
		break;
	case 'ledenmail-template':
		$template = 'ledenmail-template.twig';
		break;
	default:
		if(preg_match('/^bestuur([0-9]+)$/', $action, $m) && file_exists(TEMPLATES_DIR .'bestuur/bestuur'. $m[1] .'.twig')) {
			$action = 'bestuur'. $m[1];
			$template = 'bestuur/bestuur'. $m[1] .'.twig';
		} else {
			header('Status: 404', true, 404);
			$template = '404.twig';
		}
		break;
}

$context['action'] = $action;
if($action === 'index' || $action === 'agenda' || $action === 'ledenmail-template') {
	$agenda_json = file_get_contents(AGENDA_JSON_FILE);
	if (! $agenda_json) {
		die('Fout bij het laden van de agenda');
	}
	$context['agenda'] = json_decode($agenda_json);
}

$html = $twig->render($template, $context);

header('Content-Length: ' . strlen($html));
print $html;
